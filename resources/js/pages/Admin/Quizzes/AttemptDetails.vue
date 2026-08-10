<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft, Award, CheckCircle, XCircle, Clock, Target, User, Mail } from '@lucide/vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { computed } from 'vue';

defineOptions({
    layout: AppLayout,
});

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

interface User {
    id: number;
    name: string;
    email: string;
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
    user: User | null;
}

const props = defineProps<{
    quiz: Quiz;
    attempt: QuizAttempt;
}>();

const passed = computed(() => props.attempt.score >= props.quiz.passing_score);

const timeSpent = computed(() => {
    const start = new Date(props.attempt.started_at);
    const end = new Date(props.attempt.completed_at);
    const diffMs = end.getTime() - start.getTime();
    const diffMins = Math.floor(diffMs / 60000);
    const diffSecs = Math.floor((diffMs % 60000) / 1000);
    return `${diffMins}m ${diffSecs}s`;
});

const isQuestionCorrect = (questionId: number) => {
    const question = props.quiz.questions.find(q => q.id === questionId);
    if (!question) return false;
    return props.attempt.answers[questionId] === question.correct_answer;
};
</script>

<template>
    <Head :title="`Quiz Attempt Details - ${quiz.title}`" />

    <div class="space-y-6">
        <!-- Back Button -->
        <Link href="/admin/quiz-attempts">
            <Button variant="outline" class="gap-2">
                <ArrowLeft class="h-4 w-4" />
                Back to Attempts
            </Button>
        </Link>

        <!-- Header Card -->
        <Card :class="passed ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'">
            <CardHeader>
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <div :class="passed ? 'rounded-full bg-green-500 p-2' : 'rounded-full bg-red-500 p-2'">
                                <Award class="h-6 w-6 text-white" />
                            </div>
                            <div>
                                <CardTitle class="text-2xl">{{ quiz.title }}</CardTitle>
                                <p :class="passed ? 'text-green-700 font-semibold' : 'text-red-700 font-semibold'">
                                    {{ passed ? 'Passed' : 'Failed' }}
                                </p>
                            </div>
                        </div>

                        <!-- Student Info -->
                        <div class="flex items-start gap-2 p-3 bg-white rounded-lg border">
                            <User class="h-5 w-5 text-gray-400 mt-0.5" />
                            <div>
                                <p class="font-semibold text-gray-900 text-lg">{{ attempt.student_name || 'Anonymous' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Score Display -->
                    <div class="text-right">
                        <div :class="passed ? 'text-5xl font-bold text-green-600' : 'text-5xl font-bold text-red-600'">
                            {{ attempt.score }}%
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Final Score</p>
                    </div>
                </div>
            </CardHeader>

            <CardContent>
                <div class="grid gap-4 md:grid-cols-4">
                    <div class="p-3 bg-white rounded-lg border">
                        <div class="flex items-center gap-2 mb-1">
                            <CheckCircle class="h-4 w-4 text-green-600" />
                            <p class="text-sm text-gray-600">Correct Answers</p>
                        </div>
                        <p class="text-xl font-bold text-gray-900">{{ attempt.correct_answers }}/{{ attempt.total_questions }}</p>
                    </div>

                    <div class="p-3 bg-white rounded-lg border">
                        <div class="flex items-center gap-2 mb-1">
                            <Clock class="h-4 w-4 text-purple-600" />
                            <p class="text-sm text-gray-600">Time Spent</p>
                        </div>
                        <p class="text-xl font-bold text-gray-900">{{ timeSpent }}</p>
                    </div>

                    <div class="p-3 bg-white rounded-lg border">
                        <div class="flex items-center gap-2 mb-1">
                            <Target class="h-4 w-4 text-indigo-600" />
                            <p class="text-sm text-gray-600">Passing Score</p>
                        </div>
                        <p class="text-xl font-bold text-gray-900">{{ quiz.passing_score }}%</p>
                    </div>

                    <div class="p-3 bg-white rounded-lg border">
                        <div class="flex items-center gap-2 mb-1">
                            <XCircle class="h-4 w-4 text-red-600" />
                            <p class="text-sm text-gray-600">Wrong Answers</p>
                        </div>
                        <p class="text-xl font-bold text-gray-900">{{ attempt.total_questions - attempt.correct_answers }}</p>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Questions Review -->
        <Card>
            <CardHeader>
                <CardTitle>Answer Review</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div
                    v-for="(question, index) in quiz.questions"
                    :key="question.id"
                    class="p-4 rounded-lg border-2"
                    :class="isQuestionCorrect(question.id) ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'"
                >
                    <!-- Question Header -->
                    <div class="flex items-start gap-3 mb-3">
                        <div :class="isQuestionCorrect(question.id) ? 'rounded-full bg-green-500 p-1.5' : 'rounded-full bg-red-500 p-1.5'">
                            <CheckCircle v-if="isQuestionCorrect(question.id)" class="h-4 w-4 text-white" />
                            <XCircle v-else class="h-4 w-4 text-white" />
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <Badge :class="isQuestionCorrect(question.id) ? 'bg-green-600' : 'bg-red-600'">
                                    Question {{ index + 1 }}
                                </Badge>
                                <span class="text-sm font-semibold" :class="isQuestionCorrect(question.id) ? 'text-green-700' : 'text-red-700'">
                                    {{ isQuestionCorrect(question.id) ? 'Correct' : 'Incorrect' }}
                                </span>
                            </div>
                            <p class="text-gray-900 font-medium">{{ question.question }}</p>
                        </div>
                    </div>

                    <!-- Answers Grid -->
                    <div class="grid gap-2 ml-10">
                        <div
                            v-for="(option, key) in question.options"
                            :key="key"
                            class="flex items-start gap-2 p-3 rounded"
                            :class="
                                key === question.correct_answer
                                    ? 'bg-green-100 border-l-4 border-green-600'
                                    : key === attempt.answers[question.id] && key !== question.correct_answer
                                    ? 'bg-red-100 border-l-4 border-red-600'
                                    : 'bg-white'
                            "
                        >
                            <span class="font-bold text-gray-900 min-w-[24px]">{{ key.toUpperCase() }}.</span>
                            <span class="flex-1 font-medium text-gray-900">
                                {{ option }}
                            </span>
                            
                            <!-- Badges -->
                            <div class="flex gap-1">
                                <Badge v-if="key === question.correct_answer" class="bg-green-700 text-white font-bold">
                                    ✓ Correct
                                </Badge>
                                <Badge v-if="key === attempt.answers[question.id] && key !== question.correct_answer" class="bg-red-700 text-white font-bold">
                                    ✗ Your Answer
                                </Badge>
                            </div>
                        </div>
                    </div>

                    <!-- Explanation -->
                    <div v-if="question.explanation" class="ml-10 mt-3 p-3 bg-blue-50 border-l-4 border-blue-500 rounded">
                        <p class="text-xs font-semibold text-blue-900 mb-1">💡 Explanation:</p>
                        <p class="text-sm text-blue-800">{{ question.explanation }}</p>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
