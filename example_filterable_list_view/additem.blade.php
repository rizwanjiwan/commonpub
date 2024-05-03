<?php

use \rizwanjiwan\common\web\RequestHandler;
use \rizwanjiwan\common\web\ValidatableKeys;

//todo: get initial errors into JS
$csrf = new \rizwanjiwan\common\classes\Csrf();
//start with importing vue
?>

@if(strcmp(\rizwanjiwan\common\classes\Config::get('ENV'),'dev')===0)
    <script src="vue.global.js"></script>
@else
    <script src="vue.global.prod.js"></script>
@endif

<div class="rounded border p-10">
    <form method="POST" action="{{RequestHandler::getUrl('addItemPost')}}" id="itemForm">
        <input type="hidden" value="{{$csrf->getToken()}}" name="{{\rizwanjiwan\common\classes\Csrf::CSRF_TOKEN_KEY}}"/>

        <!--begin::Input group: name-->
        <div class="fv-row mb-10 fv-plugins-icon-container">
            <label for="{{ItemController::ITEM_NAME_KEY}}"
                   class="form-label fs-6 fw-bold text-dark required">
                Name
            </label>
            <input id="{{ItemController::ITEM_NAME_KEY}}"
                   @keyup="validateField('{{ItemController::ITEM_NAME_KEY}}')"
                   class="form-control form-control-lg form-control-solid"
                   name="{{ItemController::ITEM_NAME_KEY}}"
                   placeholder=""
                   required
                   autofocus/>
            <div class="fv-plugins-message-container invalid-feedback" v-for="error in nameErrors">@{{ error }}</div>
        </div>
        <!--end::Input group: name-->

        <!--begin::Input group: description-->
        <div class="fv-row mb-10 fv-plugins-icon-container">
            <label for="{{ItemController::ITEM_DESCRIPTION_KEY}}"
                   class="form-label fs-6 fw-bold text-dark required">
                Email
            </label>
            <input id="{{ItemController::ITEM_DESCRIPTION_KEY}}"
                   @keyup="validateField('{{ItemController::ITEM_DESCRIPTION_KEY}}')"
                   class="form-control form-control-lg form-control-solid"
                   type="email"
                   name="{{ItemController::ITEM_DESCRIPTION_KEY}}"
                   placeholder=""
                   required
                   autofocus/>
            <div class="fv-plugins-message-container invalid-feedback" v-for="error in emailErrors">@{{ error }}</div>
        </div>
        <!--end::Input group: description-->


        <!--begin::Actions-->
        <div class="text-center">
            <!--begin::Submit button-->
            <input type="submit" class="btn btn-lg btn-primary w-100 mb-5" value="Add Item"
                   :disabled="isDisabled"></input>
            <!--end::Submit button-->
        </div>
        <!--end::Actions-->
    </form>

</div>
<script src="form-validator.js"></script>

<script>
    formValidator.provide('csrfToken', "{{$csrf->getToken()}}");
    formValidator.provide('validateUrl', "{{RequestHandler::getUrl('itemValidate')}}")

    let fieldsToValidate = [
        {
            input: "{{ItemController::ITEM_NAME_KEY}}",
            errors: "{{ItemController::ITEM_NAME_KEY}}Errors",
            initialErrors: {!! json_encode(array()) !!},
            required: true
        },
        {
            input: "{{ItemController::ITEM_DESCRIPTION_KEY}}",
            errors: "{{ItemController::ITEM_DESCRIPTION_KEY}}Errors",
            initialErrors: {!! json_encode(array()) !!},
            required: true
        }
    ]
    formValidator.provide('toValidate', fieldsToValidate)
    const formValidatorMount = formValidator.mount('#itemForm');
</script>