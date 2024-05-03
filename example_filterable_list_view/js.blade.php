<?php
//HTML example for views where the controller implements SearchTrait

use  rizwanjiwan\common\web\SearchKeys;
use rizwanjiwan\common\classes\FieldContainer;
use rizwanjiwan\common\web\fields\SelectField;

/**
 * @var $fields FieldContainer of fields you want to work with
 * @var $appTagId string the id to use on this
 * @var $searchEndpoint string the url to make json calls to
 * @var $downloadEndpoint string the url to pop open when downloading
 * @var SelectField[]|null $dropDownFilters of values for the dropdown filters
 */
$fieldArray = array();
foreach ($fields as $field) {
    $element = ["key" => $field->getUniqueName(), "friendlyName" => $field->getFriendlyName()];
    array_push($fieldArray, $element);
}
$filterArray = array();
foreach ($dropDownFilters as $filter) {
    array_push($filterArray, $filter->getUniqueName());
}
$csrf = new \rizwanjiwan\common\classes\Csrf();


//need to start with vue.js either here or globally
?>


@if(strcmp(\rizwanjiwan\common\classes\Config::get('ENV'),'dev')===0)
    <script src="vue.global.js"></script>
@else
    <script src="vue.global.prod.js"></script>
@endif

<script>
    const {{$appTagId}}_filterableList = Vue.createApp({
        inject: ['fields', 'filters', 'csrfToken', 'filterUrl'], //fields=[{key:unique name, friendlyName:friendly name},], csrfToken string
        data() {
            return {
                searchTerm: "",
                sortField:null,
                sortDirection:0,
                loading: true,
                rows: [],
                pages: [],
                currentPage: 1,
                nextSequence: 0,
                currDisplaySequence: 0,
                searchTimer: null
            };
        },
        methods: {
            updateSearch() {
                //timer exists to keep multiple calls from happening while someone is typing quickly
                //rest of this method is to update the ui as we are about to make a call to the server
                console.log('Update Search Called')
                clearTimeout(this.searchTimer);
                this.loading = true;
                this.searchTimer = setTimeout(() => {
                    this.updateSearchReal();
                }, 200);
            },
            updateSearchReal() {

                this.currDisplaySequence = this.nextSequence;//if multiple calls happen, only update the display for the latest call
                this.nextSequence++;
                const filtersObj = this.filters.reduce((accumulator, currentValue) => {   //mash all the filter values into an array
                    accumulator[currentValue] = document.getElementById(currentValue).value
                    return accumulator;
                }, {})
                fetch(this.filterUrl, {
                    method: 'post',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        {{\rizwanjiwan\common\classes\Csrf::CSRF_TOKEN_KEY}}: this.csrfToken,
                        {{SearchKeys::SEARCH}}: this.searchTerm,
                        {{SearchKeys::FILTERS}}: filtersObj,
                        {{SearchKeys::SORT}}:this.sortField,
                        {{SearchKeys::SORT_DIRECTION}}:this.sortDirection,
                        {{SearchKeys::PAGE_NUM}}: this.currentPage,
                        {{SearchKeys::SEQUENCE_NUM}}: this.nextSequence
                    }),
                }).then(
                    (response) => {
                        response.json().then((obj) => {
                            if (obj.hasOwnProperty('error')) {
                                alert(obj.error);
                            } else {
                                //alert(JSON.stringify(obj));
                                // {"__sq":1,"__payload":{"__page":0,"__pages":[1,2],"__rows":[{"name":"name","email":"a@b.ca","dob":"1970-01-01}]}}
                                const seqNum = parseInt(obj.{{SearchKeys::SEQUENCE_NUM}});

                                if (seqNum > this.currDisplaySequence)//prevent slow returns from showing after future returns come back
                                {
                                    const payload = obj.{{SearchKeys::PAYLOAD}}//extract payload
                                    this.currentPage = parseInt(payload.{{SearchKeys::PAGE_NUM}})
                                    this.pages.splice(0, this.pages.length);//clear array
                                    payload.{{SearchKeys::PAGES}}.forEach((el) => {//add in pages
                                        this.pages.push(el);
                                    });
                                    //clear rows and add them all in from response
                                    this.rows.splice(0, this.rows.length);//clear array
                                    payload.{{SearchKeys::ROWS}}.forEach((el) => {//add in pages
                                        this.rows.push(el);
                                    });
                                    this.loading = false;
                                    //console.log('done load')
                                }//end if sequence number

                            }//end else

                        })
                    });

            },//end updateSearch
            goPage(page) {//jump to a page
                clearTimeout(this.searchTimer);
                this.currentPage = page;
                this.loading = true;
                this.updateSearchReal();
            },
            sort(field){
                if(this.sortField!==field){  //new sort field
                    this.sortField=field
                    this.sortDirection=0;
                }
                else { //same field was clicked
                    if(this.sortDirection===1){
                        //already been clicked twice, reset
                        this.sortField=null
                        this.sortDirection=0;
                    }
                    else{
                        //only clicked once, swap direction
                        this.sortDirection=1;
                    }
                }
                //refresh results
                clearTimeout(this.searchTimer);
                this.updateSearch();

            },
            download(){ //open the download view with right params
                const filtersObj = this.filters.reduce((accumulator, currentValue) => {   //mash all the filter values into an array
                    accumulator[currentValue] = document.getElementById(currentValue).value
                    return accumulator;
                }, {})
                let targetUrl="{{$downloadEndpoint}}/?{{\rizwanjiwan\common\classes\Csrf::CSRF_TOKEN_KEY}}="+encodeURIComponent(this.csrfToken)+
                    "&{{SearchKeys::SEARCH}}="+encodeURIComponent(this.searchTerm)+
                    "&{{SearchKeys::FILTERS}}="+encodeURIComponent(JSON.stringify(filtersObj));
                if(this.sortField!==null){
                    targetUrl= targetUrl+"&{{SearchKeys::SORT}}="+encodeURIComponent(this.sortField)+
                        "&{{SearchKeys::SORT_DIRECTION}}="+encodeURIComponent(this.sortDirection);

                }
                //console.log();
                window.open(targetUrl,"_blank");
            }
        },//end method
        computed: {
            nothingFound() {
                return this.rows.length === 0;
            },
            showPagination() {
                return this.loading === false && this.pages.length > 1;
            },
        }
    });
    {{$appTagId}}_filterableList.provide('csrfToken', "{{$csrf->getToken()}}");
    {{$appTagId}}_filterableList.provide('fields', {!! json_encode($fieldArray) !!});
    {{$appTagId}}_filterableList.provide('filters', {!! json_encode($filterArray) !!});
    {{$appTagId}}_filterableList.provide('filterUrl', "{{$searchEndpoint}}");

    const {{$appTagId}}_filterableListMount = {{$appTagId}}_filterableList.mount('#{{$appTagId}}');
    {{$appTagId}}_filterableListMount.updateSearchReal();
</script>