<script setup>
import { Head, useForm } from "@inertiajs/vue3";

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head title="Login" />
    <main class="min-h-screen">
        <form @submit.prevent="submit">Login</form>
    </main>
</template>
