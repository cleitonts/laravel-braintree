<template>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h2 class="float-left">New Transaction</h2>
                <a href="/home" class="btn float-right btn-primary">return</a>
            </div>
            <div class="card-body mb-4 border-bottom">
                <div class="form-group row">
                    <label for="amount" class="col-sm-2 col-form-label">amount</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="amount" v-model="dados.amount">
                        <small>ex: 10.00</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label for="billing-name" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="billing-name" v-model="dados.name">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="customer-email" class="col-sm-2 col-form-label">Email Address</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="customer-email" v-model="dados.email">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="customer-phone" class="col-sm-2 col-form-label">Phone Number</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="customer-phone" v-model="dados.phone">
                    </div>
                </div>

                <div class="col-12">
                    <div id="dropin-container"></div>

                    <button type="submit" class="btn btn-info btn-block my-2" @click="dropinRequestPaymentMethod">Save user and submit payment</button>
                    <strong style="color:red;">{{errorMessage}}</strong>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'NewTransaction',
    props: {
        token: { value: String},
        user: {value: String},
    },
    data () {
        return {
            merchantAccountId: 'whildcaught',
            paymentMethodNonce: '',
            paymentMethodToken: '',
            paypal: '',
            errorMessage: '',
            dropinInstance: '',
            paymentPayload: '',
            dataCollectorPayload: '',
            collectCardHolderName: true,
            enableDataCollector: true,
            enablePayPal: false,
            dados: this.user
        }
    },
    methods: {
        dropinCreate() {
            const dropin = require('braintree-web-drop-in');
            // setup drop-in options
            const dropinOptions = {
                authorization: this.token,
                selector: '#dropin-container',
            }
            // if PayPal enabled, add to options settings
            if (this.enablePayPal) {
                dropinOptions.paypal = {
                    flow: 'vault'
                };
            }

            dropin.create(dropinOptions, (dropinError, dropinInstance) => {
                this.errorMessage = '';
                if (dropinError) {
                    this.errorMessage = ('There was an error setting up the client instance. Message: ' + dropinError.message);
                    return;
                }
                this.dropinInstance = dropinInstance;
            });
        },
        dropinRequestPaymentMethod() {
            let el = this;
            this.dropinInstance.requestPaymentMethod((requestErr, payload) => {
                if (requestErr) {
                    this.errorMessage = ('There was an error setting up the client instance. Message: ' + requestErr.message);
                    return;
                }
                this.paymentPayload = payload;
                // do something with the payload/nonce

                let formData = new FormData();
                formData.append('nonce', this.paymentPayload.nonce);
                for(var i in this.dados){
                    formData.append(i, this.dados[i]);
                }

                axios({
                    method: "post",
                    url: "/transaction/store",
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
            });
        },
    },
    created() {
        this.dropinCreate();
    }
}
</script>
