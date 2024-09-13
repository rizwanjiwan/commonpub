<?php
//HTML example for views where the controller implements SearchTrait
use rizwanjiwan\common\classes\FieldContainer;
use rizwanjiwan\common\web\fields\SelectField;

/**
 * @var string $appTagId the outermost div id you'd like to use
 * @var FieldContainer $fields AbstractField you'd like to display with unique name=api call key. Values aren't used
 * @var string $linkUrl where to link them to when they click (will slap on the id into the get params)
 * @var SelectField[]|null $dropDownFilters of values for the dropdown filters
 * @var string $linkKeyId the name of the "ID" key
 */

?>
<div id="{{$appTagId}}" class="container text-center">
    <div class="row">
        <div class="card-title">
            <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1">
                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                <span class="svg-icon svg-icon-1 position-absolute ms-6"><svg width="24" height="24" viewBox="0 0 24 24"
                                                                              fill="none"
                                                                              xmlns="http://www.w3.org/2000/svg">
<rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)"
      fill="currentColor"></rect>
<path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
      fill="currentColor"></path>
</svg>
</span>
                <!--end::Svg Icon-->
                <input type="text"
                       data-kt-user-table-filter="search"
                       class="form-control form-control-solid w-250px ps-14"
                       placeholder="Search"
                       v-model="searchTerm"
                       v-on:keyup="updateSearch">
                <!--end::Search-->
                <!--start filters-->
                <div class="card-toolbar p-5" data-select2-id="select2-data-196-omz7">
                    <!--begin::Toolbar-->
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        @if(isset($dropDownFilters)&&(count($dropDownFilters)>0))
                            <!--begin::Filter-->
                            <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click"
                                    data-kt-menu-placement="bottom-end">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                                <span class="svg-icon svg-icon-2"><svg width="24" height="24" viewBox="0 0 24 24"
                                                                       fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
      fill="currentColor"></path>
</svg>
</span>
                                <!--end::Svg Icon-->        Filter
                            </button>
                            <!--begin::Menu 1-->
                            <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true"
                                 style="">
                                <!--begin::Header-->
                                <div class="px-7 py-5">
                                    <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                </div>
                                <!--end::Header-->

                                <!--begin::Separator-->
                                <div class="separator border-gray-200"></div>
                                <!--end::Separator-->

                                <!--begin::Content-->
                                @foreach($dropDownFilters as $filter)
                                    <div class="px-7 py-5" data-kt-user-table-filter="form"
                                         data-select2-id="select2-data-195-2tqx">
                                        <!--begin::Input group-->
                                        <div class="mb-10" data-select2-id="select2-data-194-sud5">
                                            <label class="form-label fs-6 fw-semibold">{{$filter->getFriendlyName()}}
                                                :</label>
                                            <select
                                                    name="{{$filter->getUniqueName()}}"
                                                    id="{{$filter->getUniqueName()}}"
                                                    class="form-select form-select-solid fw-bold"
                                                    data-placeholder="Select option"
                                                    data-hide-search="true"
                                                    @change="updateSearch">
                                                @foreach($filter->getOptions() as $option)
                                                    <option value="{{$option->getUniqueName()}}" {{$option->selectedByDefault?'selected':''}} >{{$option->getFriendlyName()}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!--end::Input group-->
                                    </div>
                                @endforeach
                                <!--end::Content-->
                            </div>
                            <!--end::Menu 1-->

                        @endif
                    </div>    <!--end filters-->

                    <!--end::Toolbar-->
                </div>
            </div>
        </div>
        <div class="row" style="padding-bottom:10px;"></div>
        <div class="row" v-show="loading">
            <div class="col"></div>
            <div class="col align-self-center">
                <span class="spinner-border text-primary" role="status"></span>
            </div>
            <div class="col"></div>

        </div>
        <div class="row" v-show="nothingFound" style="display:none;">
            <div class="col"></div>
            <div class="col align-self-center">
                <p>Nothing found...</p>
            </div>
            <div class="col"></div>
        </div>
        <div class="row" v-show="!loading & !nothingFound" style="display:none;">
            <div class="table-responsive">
                <table class="table table-rounded table-striped border gy-7 gs-7">
                    <thead>
                    <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                        <th v-for="field in fields" v-on:click="sort(field.key)" class="pointer">
                            <span v-show="sortField===field.key && sortDirection===0"><i class="bi bi-sort-down"></i></span>
                            <span v-show="sortField===field.key && sortDirection===1"><i class="bi bi-sort-up"></i></span>
                            @{{ field.friendlyName }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="row in rows">
                        <td v-for="field in fields">
                            <a v-bind:href="'{{$linkUrl}}?{{$linkKeyId}}='+row.{{$linkKeyId}}">
                                @{{ row[field.key] }}
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-show="showPagination" style="display:none;">
            <ul class="pagination">
                <li v-on:click="goPage(currentPage-1)" class="page-item previous"
                    v-bind:class="{disabled:currentPage===1}"><a class="page-link"><i class="previous"></i></a></li>
                <li v-for="pg in pages" class="page-item" v-on:click="goPage(pg)"
                    v-bind:class="{active:currentPage===pg}"><a class="page-link">@{{pg}}</a></li>
                <li v-on:click="goPage(currentPage+1)" class="page-item next"
                    v-bind:class="{disabled:currentPage===pages.length}"><a class="page-link"><i class="next"></i></a>
                </li>
            </ul>
        </div>

    </div>
</div>