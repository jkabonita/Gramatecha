<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Award, CheckCircle, XCircle, Clock, Target, TrendingUp, RotateCcw, Home } from '@lucide/vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { computed } from 'vue';

interface QuizQuestion {
    id: number;
    question: string;
    options: Record<string, string>;
    correct_answer: string;
    explanation?: string | null;
}

interface Quiz {
    id: number;
    title: string;
    slug: string;
    description?: string;
    passing_score: number;
    questions: QuizQuestion[];
}

interface QuizAttempt {
    id: number;
    score: number;
    correct_answers: number;
    total_questions: number;
    answers: Record<number, string>;
    student_name?: string;
    started_at: string;
    completed_at: string;
}

const props = defineProps<{
    quiz: Quiz;
    attempt: QuizAttempt;
}>();

defineOptions({
    layout: PublicLayout,
});

const passed = computed(() => props.attempt.score >= props.quiz.passing_score);
const timeSpent = computed(() => {
    const start = new Date(props.attempt.started_at);
    const end = new Date(props.attempt.completed_at);
    const diffMs = end.getTime() - start.getTime();
    const diffMins = Math.floor(diffMs / 60000);
    const diffSecs = Math.floor((diffMs % 60000) / 1000);
    return `${diffMins}m ${diffSecs}s`;
});

const getAnswerClass = (questionId: number, optionKey: string) => {
    const question = props.quiz.questions.find(q => q.id === questionId);
    if (!question) return '';

    const userAnswer = props.attempt.answers[questionId];
    const isCorrectAnswer = optionKey === question.correct_answer;
    const isUserAnswer = optionKey === userAnswer;

    if (isCorrectAnswer && isUserAnswer) {
        return 'border-green-600 bg-green-50';
    } else if (isCorrectAnswer) {
        return 'border-green-600 bg-green-50';
    } else if (isUserAnswer) {
        return 'border-red-600 bg-red-50';
    }
    return 'border-gray-200 bg-gray-50';
};

const getAnswerIcon = (questionId: number, optionKey: string) => {
    const question = props.quiz.questions.find(q => q.id === questionId);
    if (!question) return null;

    const userAnswer = props.attempt.answers[questionId];
    const isCorrectAnswer = optionKey === question.correct_answer;
    const isUserAnswer = optionKey === userAnswer;

    if (isCorrectAnswer) {
        return CheckCircle;
    } else if (isUserAnswer) {
        return XCircle;
    }
    return null;
};

const isQuestionCorrect = (questionId: number) => {
    const question = props.quiz.questions.find(q => q.id === questionId);
    if (!question) return false;
    return props.attempt.answers[questionId] === question.correct_answer;
};
</script>

<template>
    <Head :title="`Results: ${quiz.title}`" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-12">
        <div class="container mx-auto px-4 max-w-5xl">
            <!-- Results Header -->
            <Card class="mb-8 overflow-hidden shadow-lg bg-white">
                <div :class="[
                    'p-8 text-center',
                    passed ? 'bg-gradient-to-br from-green-500 via-green-600 to-emerald-600' : 'bg-gradient-to-br from-orange-500 via-orange-600 to-red-600'
                ]">
                    <div class="flex justify-center mb-4">
                        <div class="rounded-full bg-white p-4 shadow-lg">
                            <Award :class="passed ? 'h-12 w-12 text-green-600' : 'h-12 w-12 text-orange-600'" />
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        {{ passed ? 'Congratulations!' : 'Keep Practicing!' }}
                    </h1>
                    <p v-if="attempt.student_name" class="text-white text-opacity-95 text-xl font-semibold mb-1">
                        {{ attempt.student_name }}
                    </p>
                    <p class="text-white text-opacity-95 text-lg">
                        {{ passed ? 'You passed the quiz!' : 'You can try again to improve your score.' }}
                    </p>
                </div>

                <CardContent class="p-8 bg-white">
                    <div class="grid gap-6 md:grid-cols-4 mb-8">
                        <div class="text-center p-4 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100">
                            <div class="flex justify-center mb-2">
                                <Target class="h-8 w-8 text-blue-600" />
                            </div>
                            <p class="text-3xl font-bold text-blue-700">{{ attempt.score }}%</p>
                            <p class="text-sm text-blue-600 font-medium">Final Score</p>
                        </div>

                        <div class="text-center p-4 rounded-lg bg-gradient-to-br from-green-50 to-green-100">
                            <div class="flex justify-center mb-2">
                                <CheckCircle class="h-8 w-8 text-green-600" />
                            </div>
                            <p class="text-3xl font-bold text-green-700">{{ attempt.correct_answers }}/{{ attempt.total_questions }}</p>
                            <p class="text-sm text-green-600 font-medium">Correct Answers</p>
                        </div>

                        <div class="text-center p-4 rounded-lg bg-gradient-to-br from-purple-50 to-purple-100">
                            <div class="flex justify-center mb-2">
                                <Clock class="h-8 w-8 text-purple-600" />
                            </div>
                            <p class="text-3xl font-bold text-purple-700">{{ timeSpent }}</p>
                            <p class="text-sm text-purple-600 font-medium">Time Spent</p>
                        </div>

                        <div class="text-center p-4 rounded-lg bg-gradient-to-br from-indigo-50 to-indigo-100">
                            <div class="flex justify-center mb-2">
                                <TrendingUp class="h-8 w-8 text-indigo-600" />
                            </div>
                            <p class="text-3xl font-bold text-indigo-700">{{ quiz.passing_score }}%</p>
                            <p class="text-sm text-indigo-600 font-medium">Passing Score</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 justify-center">
                        <Link :href="`/quizzes/${quiz.slug}`">
                            <Button variant="outline" class="gap-2">
                                <Home class="h-4 w-4" />
                                Back to Quiz
                            </Button>
                        </Link>

                        <Link :href="`/quizzes/${quiz.slug}/start`">
                            <Button class="gap-2">
                                <RotateCcw class="h-4 w-4" />
                                Take Again
                            </Button>
                        </Link>

                        <Link href="/quizzes">
                            <Button variant="outline" class="gap-2">
                                All Quizzes
                            </Button>
                        </Link>
                    </div>
                </CardContent>
            </Card>

            <!-- Answer Review -->
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900">Answer Review</h2>
                    <p class="text-sm text-gray-600">Review your answers and see the correct solutions</p>
                </div>

                <div
                    v-for="(question, index) in quiz.questions"
                    :key="question.id"
                    class="space-y-4"
                >
                    <Card class="shadow-md border-2 bg-white" :class="isQuestionCorrect(question.id) ? 'border-green-200' : 'border-red-200'">
                        <CardHeader class="bg-gradient-to-r" :class="isQuestionCorrect(question.id) ? 'from-green-50 to-green-100' : 'from-red-50 to-red-100'">
                            <div class="flex items-start gap-4">
                                <div :class="[
                                    'shrink-0 rounded-full p-2',
                                    isQuestionCorrect(question.id) ? 'bg-green-500' : 'bg-red-500'
                                ]">
                                    <CheckCircle
                                        v-if="isQuestionCorrect(question.id)"
                                        class="h-5 w-5 text-white"
                                    />
                                    <XCircle
                                        v-else
                                        class="h-5 w-5 text-white"
                                    />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold mb-2" :class="isQuestionCorrect(question.id) ? 'text-green-700' : 'text-red-700'">
                                        Question {{ index + 1 }} - {{ isQuestionCorrect(question.id) ? 'Correct' : 'Incorrect' }}
                                    </p>
                                    <CardTitle class="text-lg leading-relaxed text-gray-900">
                                        {{ question.question }}
                                    </CardTitle>
                                </div>
                            </div>
                        </CardHeader>

                        <CardContent class="space-y-3 pt-6 bg-white">
                            <div
                                v-for="(option, key) in question.options"
                                :key="key"
                                :class="[
                                    'flex items-start gap-3 p-4 rounded-lg border-2 transition-all',
                                    getAnswerClass(question.id, key)
                                ]"
                            >
                                <component
                                    :is="getAnswerIcon(question.id, key)"
                                    v-if="getAnswerIcon(question.id, key)"
                                    class="h-5 w-5 mt-0.5"
                                    :class="key === question.correct_answer ? 'text-green-600' : 'text-red-600'"
                                />
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-800">{{ key.toUpperCase() }}.</span>
                                    <span class="ml-2 text-gray-900">{{ option }}</span>
                                    
                                    <div v-if="key === question.correct_answer" class="mt-2 text-sm text-green-700 font-semibold">
                                        ✓ Correct Answer
                                    </div>
                                    
                                    <div v-else-if="key === attempt.answers[question.id]" class="mt-2 text-sm text-red-700 font-semibold">
                                        ✗ Your Answer
                                    </div>
                                </div>
                            </div>

                            <div v-if="question.explanation" class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
                                <p class="text-sm font-semibold text-blue-900 mb-1">💡 Explanation:</p>
                                <p class="text-sm text-blue-800">{{ question.explanation }}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>
