<template>
    <div class="container">
        <div class="col-12 col-sm-8 offset-sm-2 mt-5">
            <div class="card">
                <div class="card-header bg-dark">
                    <img src="https://www.exitlag.com/img/exitlag.png" alt="exitlag-logo">
                </div>
                <div class="card-body">
                    <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
                    <div class="row">
                        <label for="inputEmail" class="sr-only">Email address</label>
                        <input type="email" id="inputEmail" class="form-control my-1" v-model="email" placeholder="Email address" required="" autofocus="">
                    </div>
                    <div class="row">
                        <label for="inputPassword" class="sr-only">Password</label>
                        <input type="password" id="inputPassword" class="form-control my-1" v-model="password" placeholder="Password" required="">
                    </div>
                    <div v-if="trial" class="row">
                        <label for="inputPasswordConfirm" class="sr-only">Confirm password</label>
                        <input type="password" id="inputPasswordConfirm" class="form-control my-1" v-model="confirm_password" placeholder="Confirm password" required="">
                    </div>
                    <div v-if="trial" class="row">
                        <label for="name" class="sr-only">Name</label>
                        <input type="text" id="name" class="form-control my-1" v-model="name" placeholder="Name" required="">
                    </div>
                    <div v-if="errorMessage" class="text-danger" v-html="errorMessage"></div>
                </div>
                <button class="btn btn-lg btn-danger btn-block square" type="button" @click="trial = !trial">Free trial</button>
                <button class="btn btn-lg btn-success btn-block square" type="button" @click="enviar">{{ trial ? 'Sign up' : 'Sign in' }}</button>
            </div>
        </div>

        <div class="py-4">
            <div class="ft-30 font-weight-normal mt-3 d-block">
                Feito por jogadores para jogadores, o <h1>ExitLag</h1> é a melhor ferramenta para <h2>otimizar sua conexão</h2>!
            </div>

            <div class="ft-20 font-weight-normal mt-3 d-block">
                Para começar a utilizar o <h3>ExitLag</h3>, crie sua conta e experimente <h4>gratuitamente</h4> por 3 dias.
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Login",
    data: function () {
        return {
            trial: false,
            email: '',
            password: '',
            confirm_password: '',
            errorMessage: '',
            name: ''
        }
    },
    methods:{
        enviar: function () {
            let url = 'http://localhost/login';
            let el = this;
            let formData = new FormData();
            formData.append('email', this.email);
            formData.append('password', this.password);

            if (this.trial){
                url = 'http://localhost/register';
                formData.append('password_confirmation', this.confirm_password);
                formData.append('name', this.name);
            }

            axios({
                method: "post",
                url: url,
                data: formData,
                headers: {
                    "Content-Type": "multipart/form-data",
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            }).then(function (response) {
                window.location.href = 'home';
            }).catch(function (response) {
                el.errorMessage = response.response.data.message;
                let error = response.response.data.errors;
                for (var i in error){
                    el.errorMessage = el.errorMessage + "<br> "+i+": "+error[i];
                }
            });
        }
    }
}
</script>

<style scoped>
    .square{
        border-radius: 0;
    }
    .ft-30 {
        font-size: 30px;
    }
    .ft-30>*, .ft-20>* {
        font-size: 30px;
        display: inline-block;
        font-weight: 500;
        line-height: 1.2 !important;
        margin: 0;
    }
    .ft-20, .ft-20>*{
        font-size: 20px;
    }
</style>
