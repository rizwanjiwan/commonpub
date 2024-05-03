/**
 * What this app does
 * -> allows for validation of input against a form using rest calls that match ValidatableControllerTrait
 * How to use it
 * -> Setup your form. Requirements for form:
 *      -> Provide an id for each input you want to validate. ID should match the name.
 *              e.g. id="email"
 *      -> For the input, set it up to call validateField(field) on blur
 *              e.g. validateField('email')
 *      -> Provide an accompanying error area that iterates over an array name you define
 *              e.g. <div v-for="error in emailErrors">@{{ error }}</div>
 * -> Setup this component and bind it by providing the input, name of the error array, initial errors, csrf token, and validation url
 *  e.g.
 *          formValidator.provide('csrfToken',"dfdafdaf32fsda");
 *         formValidator.provide('validateUrl', "/login/post/")
 *
 *         let fieldsToValidate=[
 *             {
 *                 input:"email",
 *                 errors:"emailErrors",
 *                 initialErrors:["invalid email address"]
 *             }
 *         ]
 *         formValidator.provide('toValidate', fieldsToValidate)
 *         const formValidatorMount=formValidator.mount('#loginform');
 */
const formValidator=Vue.createApp({
    inject: ['toValidate','validateUrl','csrfToken'],
    data(){
        let returnObj={
            firstTimeRunningCompute:true
        }
        //use the injected toValidate to build out our data structures for error output
        this.toValidate.forEach((el)=>{
            //console.log(el.initialErrors);
            returnObj[el.errors]=el.initialErrors;
        });
        return returnObj;
    },
    methods:{
        validateField(field){   //validate a specific field
            fetch(this.validateUrl, {
                method: 'post',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    __csfr_token:this.csrfToken,
                    __VALIDATION_FIELD:field,
                    __FIELDS:this.getAllFields()
                }),
            }).then(
                (response)=> {
                    response.json().then((obj)=> {
                        if (obj.hasOwnProperty('error')){
                            alert(obj.error);
                        }
                        else{
                            //go through our injected mapping of fields to errors and match the right error
                            //field. Then set that to this new obj.
                            this.toValidate.forEach((el)=>{
                                if(field===el.input){
                                    const toExec="this."+el.errors+"=obj;";
                                    eval(toExec);
                                }
                            });
                        }//end else
                    })
                });
        },
        clearErrors(field){
            this.toValidate.forEach((el)=>{
                if(field===el.input){
                    const toExec="this."+el.errors+"=\"\";";
                    eval(toExec);
                }
            });
        },
        getAllFields(){//get all the  fields (key:value) in an array
            //this is going through all the input we got from toValidate and grb the value
            let fields={};
            this.toValidate.forEach((el)=>{
                const input = document.querySelector('#'+el.input);
                if(input!==null){
                    if(input.type==="checkbox"){    //special case for checkboxes
                        fields[el.input]=input.checked?'True':'False';
                    }
                    else{
                        fields[el.input]=input.value;
                    }
                }

            });
            return fields;
        },
        enableIsDisabled(){
            this.firstTimeRunningCompute=false;//should auto kick off isDisabled
        }
    },
    computed:{
        isDisabled(){
            if(this.firstTimeRunningCompute){
                setTimeout(()=>{this.enableIsDisabled()},150);
                return false;//need to wait for everything to load
            }
            let problemExists=false;
            this.toValidate.forEach((el)=>{
                if(el.required){//check all required fields have something
                    const input = document.querySelector('#'+el.input);
                    if((input===null)||(input.value.length===0)){
                        problemExists=true;
                    }
                }
                if(this[el.errors].length>0){//there are errors
                    problemExists=true;
                }
            });
            //nothing wrong
            return problemExists;
        }
    }
});