<template>
    <div class="container-fluid">
        <div v-if="errorMessage" class="alert alert-danger" role="alert">
            {{errorMessage}}
        </div>
        <div class="card">
            <div class="card-header"><h2>Home</h2></div>

            <div v-if="list_subscription" class="card-body">
                <h5>Active subscriptions</h5>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>cancel</th>
                        <th>status</th>
                        <th>balance</th>
                        <th>price</th>
                        <th>billingDayOfMonth</th>
                        <th>nextBillAmount</th>
                        <th>firstBillingDate</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, index) in list_subscription" :key="index">
                        <td><button class="btn btn-danger btn-sm" @click="cancelSubscription(item.id)">x</button></td>
                        <td>{{item.status}}</td>
                        <td>{{item.balance}}</td>
                        <td>{{item.price}}</td>
                        <td>{{item.billingDayOfMonth}}</td>
                        <td>{{item.nextBillAmount}}</td>
                        <td>{{item.firstBillingDate.date}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-body">
                <h5>Plan list</h5>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Join</th>
                        <th>name</th>
                        <th>billingFrequency</th>
                        <th>currencyIsoCode</th>
                        <th>price</th>
                        <th>description</th>
                        <th>trialDuration</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, index) in list_plans" :key="index">
                        <td><button class="btn btn-warning btn-sm" @click="saveSubscription(item.id)">+</button></td>
                        <td>{{item.name}}</td>
                        <td>{{item.billingFrequency}}</td>
                        <td>{{item.currencyIsoCode}}</td>
                        <td>{{item.price}}</td>
                        <td>{{item.description}}</td>
                        <td>{{item.trialDuration}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-body">
                <a class="btn btn-info btn-block mb-3" href="/new">New Transaction / Customer id</a>
                <h5>Transactions list</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Status</th>
                            <th>Currency</th>
                            <th>Amount</th>
                            <th>Created at</th>
                            <th>Payment type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in list" :key="index">
                            <td>
                                {{item.id}}
                            </td>
                            <td>
                                {{item.status}}
                            </td>
                            <td>
                                {{item.currencyIsoCode}}
                            </td>
                            <td>
                                {{item.amount}}
                            </td>
                            <td>
                                {{item.createdAt.date}}
                            </td>
                            <td>
                                {{item.paymentInstrumentType}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Listing',
    props: {
        items: {value: String},
        plan_list: {value: String},
        subscription_list: {value: String},
    },
    data () {
        return {
            errorMessage: '',
            list: JSON.parse(this.items),
            list_plans: JSON.parse(this.plan_list),
            list_subscription: JSON.parse(this.subscription_list)
        }
    },
    methods: {
        cancelSubscription: function (val) {
            let el = this;
            let formData = new FormData();
            formData.append('code', val);

            axios({
                method: "post",
                url: "/subscription/cancel",
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
                    document.location.reload(true);
                }
            }).catch(function (response) {
                //handle error
                console.log(response);
            });
        },
        saveSubscription: function(val){
            let el = this;
            let formData = new FormData();
            formData.append('plan', val);

            axios({
                method: "post",
                url: "/subscription/store",
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
                    document.location.reload(true);
                }
            }).catch(function (response) {
                //handle error
                console.log(response);
            });
        }
    }
}
</script>
