<template>
    <div class="my-3">
        <strong>{{name}}</strong>
        <div class="form-check" v-for="(item, index) in opt" :key="index">
            <input class="form-check-input" :value="item" @click="onChange($event)"
                   type="radio" :name="name" :id="'id_'+name+'_'+index" :checked="(modelo==item)">
            <label class="form-check-label" :for="'id_'+name+'_'+index">
                {{item}}
            </label>
        </div>
    </div>
</template>

<script>
export default {
    name: "RadioInput",
    props: ['name', 'modelo', 'opt'],
    methods: {
        onChange: function (event) {
            this.$emit("update:modelo", event.target.value);
        },
    },
}
</script>
