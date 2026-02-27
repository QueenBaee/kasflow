<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-green-50 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-blue-600 mb-2">Kasflow</h1>
                    <p class="text-gray-600">ISP Management System</p>
                </div>

                <form @submit.prevent="handleSubmit">
                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email
                            </label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-500': form.errors.email }"
                                placeholder="your@email.com"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Password
                            </label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-500': form.errors.password }"
                                placeholder="••••••••"
                            />
                            <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                        </div>

                        <BaseButton
                            type="submit"
                            variant="primary"
                            size="lg"
                            :loading="form.processing"
                            class="w-full"
                        >
                            Login
                        </BaseButton>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <Link href="/register" class="text-sm text-blue-600 hover:text-blue-700">
                        Don't have an account? Register
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import BaseButton from '../../Components/BaseButton.vue';

const form = useForm({
    email: '',
    password: '',
});

const handleSubmit = () => {
    form.post('/login', {
        onSuccess: () => form.reset('password'),
    });
};
</script>
