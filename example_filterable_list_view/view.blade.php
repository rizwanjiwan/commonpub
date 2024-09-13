<?php
use rizwanjiwan\common\classes\FieldContainer;
use rizwanjiwan\common\web\fields\SelectField;

/**
 * @var $fields FieldContainer of fields you want to work with
 * @var $appTagId string the id to use on this
 * @var $searchEndpoint string the url to make json calls to
 * @var SelectField[]|null $dropDownFilters of values for the dropdown filters
 */

@include('components.filterablelist.html',[
        'appTagId'=>'list',
        'linkUrl'=>\rizwanjiwan\common\web\RequestHandler::getUrl('viewItem'),
        'fields'=>$fields,
        'dropDownFilters'=>$filters,
        'linkKeyId'=>'id'
        ])

@include('components.filterablelist.js',[
       'appTagId'=>'list',
       'fields'=>$fields,
       'dropDownFilters'=>$filters,
       'searchEndpoint'=>\rizwanjiwan\common\web\RequestHandler::getUrl('filterItemApi')
       ])