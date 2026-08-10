<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, Clock, Target, Trophy, CheckCircle } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({
    layout: PublicLayout
});

interface Quiz {
    id: number;
    title: string;
    description: string;
    slug: string;
    time_limit: number | null;
    passing_score: number;
    questions_count: number;
    user_attempts_count?: number;
    best_score?: number;
    last_attempt?: {
        score: number;
        created_at: string;
    };
}

defineProps<{
    quizzes: Quiz[];
}>();
</script>

<template>
    <Head title="Quizzes - Gramatecha" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
        <!-- Header -->
        <div class="border-b border-blue-100 bg-white/80 backdrop-blur-sm">
            <div class="w-full max-w-6xl mx-auto px-4 py-10 sm:px-6">
                <div class="text-center">
                    <div class="flex justify-center mb-4">
                        <div class="rounded-full bg-blue-100 p-4">
                            <BookOpen class="h-10 w-10 text-blue-600" />
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl lg:text-5xl">
                        Filipino Language Quizzes
                    </h1>
                    <p class="mt-4 text-base text-gray-600 max-w-2xl mx-auto sm:text-lg">
                        Test your knowledge and improve your understanding of Wikang Filipino
                    </p>
                </div>
            </div>
        </div>

        <!-- Quizzes Grid -->
        <div class="w-full max-w-6xl mx-auto px-4 py-10 sm:px-6">
            <div v-if="quizzes.length === 0" class="text-center py-12">
                <BookOpen class="h-16 w-16 text-gray-400 mx-auto mb-4" />
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Quizzes Available</h3>
                <p class="text-gray-600">Check back later for new quizzes!</p>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="quiz in quizzes"
                    :key="quiz.id"
                    class="group hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border-blue-100"
                >
                    <CardHeader>
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1">
                                <CardTitle class="text-xl mb-2 group-hover:text-blue-600 transition-colors">
                                    {{ quiz.title }}
                                </CardTitle>
                                <CardDescription class="line-clamp-2">
                                    {{ quiz.description }}
                                </CardDescription>
                            </div>
                            <div v-if="quiz.best_score !== undefined" class="shrink-0">
                                <Badge variant="secondary" class="bg-green-100 text-green-700">
                                    <Trophy class="h-3 w-3 mr-1" />
                                    {{ quiz.best_score }}%
                                </Badge>
                            </div>
                        </div>
                    </CardHeader>

                    <CardContent class="space-y-3">
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <div class="flex items-center gap-1.5">
                                <BookOpen class="h-4 w-4 text-blue-600" />
                                <span>{{ quiz.questions_count }} Questions</span>
                            </div>
                            <div v-if="quiz.time_limit" class="flex items-center gap-1.5">
                                <Clock class="h-4 w-4 text-orange-600" />
                                <span>{{ quiz.time_limit }} min</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 text-sm text-gray-600">
                            <Target class="h-4 w-4 text-green-600" />
                            <span>Passing Score: {{ quiz.passing_score }}%</span>
                        </div>

                        <div v-if="quiz.user_attempts_count !== undefined && quiz.user_attempts_count > 0">
                            <div class="flex items-center gap-1.5 text-sm text-gray-600">
                                <CheckCircle class="h-4 w-4 text-blue-600" />
                                <span>{{ quiz.user_attempts_count }} attempt{{ quiz.user_attempts_count > 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                    </CardContent>

                    <CardFooter>
                        <Button
                            as-child
                            class="w-full group-hover:bg-blue-700 transition-colors"
                        >
                            <Link :href="`/quizzes/${quiz.slug}`">
                                {{ quiz.user_attempts_count && quiz.user_attempts_count > 0 ? 'View Quiz' : 'Start Quiz' }}
                            </Link>
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </div>
    </div>
</template>
