<template>
    <el-main>
        <el-col :span="6">
            <admin-side-menu></admin-side-menu>
        </el-col>
        <el-col :span="17" :offset="1">
            <router-view></router-view>
        </el-col>
    </el-main>
</template>

<script>
    export default {
        methods: {

        }
    }
</script>

<style>

</style>