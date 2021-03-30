<template>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h2 class="float-left">Satisfaction questionnaire</h2>
                <a href="/home" class="btn float-right btn-primary">return</a>
            </div>
            <div class="card-body">
                <h5>Please rate with 1 for unsatisfied and 5 for very satisfied</h5>
                <small>All questions must be answered</small>
                <p class="border-top border-bottom p-3 m-3"><strong>Backend and database</strong></p>
                <radio-input :opt="default_range" name="PHP knowledge" :modelo.sync="dados.php"></radio-input>
                <radio-input :opt="default_range" name="Laravel knowledge" :modelo.sync="dados.laravel"></radio-input>
                <radio-input :opt="default_range" name="Development techniques" :modelo.sync="dados.dev_tech"></radio-input>
                <radio-input :opt="default_range" name="SQL knowledge" :modelo.sync="dados.sql"></radio-input>
                <p class="border-top border-bottom p-3 m-3"><strong>General</strong></p>
                <radio-input :opt="default_range" name="Code health" :modelo.sync="dados.code"></radio-input>
                <radio-input :opt="default_range" name="Docker" :modelo.sync="dados.docker"></radio-input>
                <radio-input :opt="default_range" name="Azure" :modelo.sync="dados.azure"></radio-input>
                <small class="d-block" style="margin-bottom: -15px;">considering that a lot of things was new for me</small>
                <radio-input :opt="default_range" name="Expertise to learn new things" :modelo.sync="dados.new_things"></radio-input>
                <radio-input :opt="default_range" name="Vue framework knowledge" :modelo.sync="dados.vue"></radio-input>
                <radio-input :opt="default_range" name="Javascript and general frontend knowledge" :modelo.sync="dados.frontend"></radio-input>
                <radio-input :opt="default_range" name="English (up to now)" :modelo.sync="dados.english"></radio-input>

                <div class="my-3">
                    <strong>Considerations</strong>
                    <small>Let me know more about your impressions about me, even if they are not good (250)</small>
                    <div class="form-group">
                        <textarea class="form-control" name="considerations" id="id_considerations" rows="5" v-model="dados.considerations">

                        </textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-block my-2" @click="submitForm()">Save data</button>
            </div>
        </div>
    </div>
</template>

<script>
import RadioInput from "./RadioInput";

export default {
    name: "Satisfaction",
    props: ['dados'],
    components: { RadioInput },
    data: function () {
        return {

            errorMessage: '',
            default_range: [1,2,3,4,5],

        }
    },
    methods: {
        submitForm: function () {
            let el = this;

            let formData = new FormData();
            // formData.append('nonce', this.paymentPayload.nonce);
            for(var i in this.dados){
                formData.append(i, this.dados[i]);
            }

            axios({
                method: "post",
                url: "/satisfaction/store",
                data: formData,
                headers: {
                    "Content-Type": "multipart/form-data",
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            }).then(function (response) {
                if(!response.data.status){
                    el.errorMessage = response.data.message;
                }
                else{
                    window.location.href = 'home';
                }
            }).catch(function (response) {
                //handle error
                console.log(response);
            });
        }
    }
}
</script>
