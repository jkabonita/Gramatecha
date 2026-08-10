<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { BookOpen, Clock, Target, Trophy, ArrowLeft, Play, Lock } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ref } from 'vue';

interface QuizAttempt {
    id: number;
    score: number;
    correct_answers: number;
    total_questions: number;
    student_name?: string;
    created_at: string;
}

interface Quiz {
    id: number;
    title: string;
    description: string;
    slug: string;
    time_limit: number | null;
    passing_score: number;
    attempts_allowed: number;
    show_correct_answers: boolean;
    questions_count: number;
}

const props = defineProps<{
    quiz: Quiz;
    userAttempts: QuizAttempt[] | null;
    canTakeQuiz: boolean;
}>();

const showNameDialog = ref(false);
const studentName = ref('');

const startQuiz = () => {
    showNameDialog.value = true;
};

const confirmStartQuiz = () => {
    if (!studentName.value.trim()) {
        return;
    }

    router.post(`/quizzes/${props.quiz.slug}/start`, {
        student_name: studentName.value,
    });
};

const bestScore = props.userAttempts && props.userAttempts.length > 0
    ? Math.max(...props.userAttempts.map(a => a.score))
    : null;

const isPassed = bestScore !== null && bestScore >= props.quiz.passing_score;
</script>

<template>
    <Head :title="`${quiz.title} - Gramatecha`" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
        <div class="w-full max-w-5xl mx-auto px-4 py-6 sm:py-8 sm:px-6">
            <!-- Back Button -->
            <Button as-child variant="ghost" class="mb-6">
                <Link href="/quizzes">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Back to Quizzes
                </Link>
            </Button>

            <div class="grid gap-8 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Quiz Info -->
                    <Card class="border-blue-100">
                        <CardHeader>
                            <div class="flex items-start gap-4">
                                <div class="rounded-full bg-blue-100 p-3">
                                    <BookOpen class="h-8 w-8 text-blue-600" />
                                </div>
                                <div class="flex-1">
                                    <CardTitle class="text-3xl mb-2">{{ quiz.title }}</CardTitle>
                                    <CardDescription class="text-base">
                                        {{ quiz.description }}
                                    </CardDescription>
                                </div>
                            </div>
                        </CardHeader>

                        <CardContent class="space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="flex items-center gap-3 p-3 rounded-lg bg-blue-50">
                                    <BookOpen class="h-5 w-5 text-blue-600" />
                                    <div>
                                        <p class="text-sm text-gray-600">Questions</p>
                                        <p class="font-semibold text-gray-900">{{ quiz.questions_count }}</p>
                                    </div>
                                </div>

                                <div v-if="quiz.time_limit" class="flex items-center gap-3 p-3 rounded-lg bg-orange-50">
                                    <Clock class="h-5 w-5 text-orange-600" />
                                    <div>
                                        <p class="text-sm text-gray-600">Time Limit</p>
                                        <p class="font-semibold text-gray-900">{{ quiz.time_limit }} minutes</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 p-3 rounded-lg bg-green-50">
                                    <Target class="h-5 w-5 text-green-600" />
                                    <div>
                                        <p class="text-sm text-gray-600">Passing Score</p>
                                        <p class="font-semibold text-gray-900">{{ quiz.passing_score }}%</p>
                                    </div>
                                </div>

                                <div v-if="quiz.attempts_allowed > 0" class="flex items-center gap-3 p-3 rounded-lg bg-purple-50">
                                    <Trophy class="h-5 w-5 text-purple-600" />
                                    <div>
                                        <p class="text-sm text-gray-600">Attempts Allowed</p>
                                        <p class="font-semibold text-gray-900">{{ quiz.attempts_allowed }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Start Quiz Button -->
                            <div class="pt-4">
                                <Button
                                    v-if="canTakeQuiz"
                                    @click="startQuiz"
                                    size="lg"
                                    class="w-full sm:w-auto"
                                >
                                    <Play class="mr-2 h-5 w-5" />
                                    {{ userAttempts && userAttempts.length > 0 ? 'Retake Quiz' : 'Start Quiz' }}
                                </Button>
                                <div v-else class="flex items-center gap-2 text-amber-700 bg-amber-50 border border-amber-200 p-4 rounded-lg">
                                    <Lock class="h-5 w-5" />
                                    <span>You have reached the maximum number of attempts for this quiz.</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Best Score -->
                    <Card v-if="bestScore !== null" class="border-green-100 bg-gradient-to-br from-green-50 to-white">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2 text-green-700">
                                <Trophy class="h-5 w-5" />
                                Your Best Score
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="text-center">
                                <div class="text-5xl font-bold text-green-600 mb-2">
                                    {{ bestScore }}%
                                </div>
                                <Badge v-if="isPassed" variant="secondary" class="bg-green-100 text-green-700">
                                    Passed
                                </Badge>
                                <Badge v-else variant="secondary" class="bg-amber-100 text-amber-700">
                                    Not Passed
                                </Badge>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Previous Attempts -->
                    <Card v-if="userAttempts && userAttempts.length > 0" class="border-blue-100">
                        <CardHeader>
                            <CardTitle class="text-lg">Previous Attempts</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-3">
                                <Link
                                    v-for="(attempt, index) in userAttempts.slice(0, 5)"
                                    :key="attempt.id"
                                    :href="`/quizzes/${quiz.slug}/results/${attempt.id}`"
                                    class="block p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors"
                                >
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                Attempt #{{ userAttempts.length - index }}
                                            </p>
                                            <p class="text-xs text-gray-600">
                                                {{ new Date(attempt.created_at).toLocaleDateString() }}
                                            </p>
                                        </div>
                                        <Badge
                                            variant="secondary"
                                            :class="attempt.score >= quiz.passing_score ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'"
                                        >
                                            {{ attempt.score }}%
                                        </Badge>
                                    </div>
                                </Link>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Name Entry Dialog -->
        <Dialog v-model:open="showNameDialog">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Enter Your Name</DialogTitle>
                    <DialogDescription>
                        Please enter your name before starting the quiz. This will be recorded with your results.
                    </DialogDescription>
                </DialogHeader>
                <div class="space-y-4 py-4">
                    <div class="space-y-2">
                        <Label for="student-name">Full Name</Label>
                        <Input
                            id="student-name"
                            v-model="studentName"
                            placeholder="Juan Dela Cruz"
                            @keydown.enter="confirmStartQuiz"
                            autofocus
                        />
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="showNameDialog = false">
                        Cancel
                    </Button>
                    <Button @click="confirmStartQuiz" :disabled="!studentName.trim()">
                        Start Quiz
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
