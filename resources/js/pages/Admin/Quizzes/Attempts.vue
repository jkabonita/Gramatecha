<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Award, Calendar, Clock, User, FileText, TrendingUp, CheckCircle, XCircle } from '@lucide/vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

defineOptions({
    layout: AppLayout,
});

interface User {
    id: number;
    name: string;
    email: string;
}

interface Quiz {
    id: number;
    title: string;
    slug: string;
    passing_score: number;
}

interface QuizAttempt {
    id: number;
    student_name: string | null;
    score: number;
    correct_answers: number;
    total_questions: number;
    started_at: string;
    completed_at: string;
    created_at: string;
    quiz: Quiz;
    user: User | null;
}

interface PaginatedAttempts {
    data: QuizAttempt[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

const props = defineProps<{
    attempts: PaginatedAttempts;
}>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatDuration = (startString: string, endString: string) => {
    const start = new Date(startString);
    const end = new Date(endString);
    const diffMs = end.getTime() - start.getTime();
    const diffMins = Math.floor(diffMs / 60000);
    const diffSecs = Math.floor((diffMs % 60000) / 1000);
    return `${diffMins}m ${diffSecs}s`;
};

const isPassed = (score: number, passingScore: number) => score >= passingScore;
</script>

<template>
    <Head title="Quiz Attempts - Admin" />

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Quiz Attempts</h1>
                <p class="text-gray-600 mt-1">View all student quiz attempts and results</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid gap-6 md:grid-cols-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium text-gray-600">Total Attempts</CardTitle>
                    <FileText class="h-4 w-4 text-blue-600" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-gray-900">{{ attempts.total }}</div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium text-gray-600">Pass Rate</CardTitle>
                    <TrendingUp class="h-4 w-4 text-green-600" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ attempts.data.length > 0 ? Math.round((attempts.data.filter(a => isPassed(a.score, a.quiz.passing_score)).length / attempts.data.length) * 100) : 0 }}%
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium text-gray-600">Average Score</CardTitle>
                    <Award class="h-4 w-4 text-purple-600" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ attempts.data.length > 0 ? Math.round(attempts.data.reduce((sum, a) => sum + a.score, 0) / attempts.data.length) : 0 }}%
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium text-gray-600">This Page</CardTitle>
                    <FileText class="h-4 w-4 text-indigo-600" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-gray-900">{{ attempts.data.length }}</div>
                </CardContent>
            </Card>
        </div>

        <!-- Attempts Table -->
        <Card>
            <CardHeader>
                <CardTitle>All Quiz Attempts</CardTitle>
                <CardDescription>Detailed list of all student quiz attempts</CardDescription>
            </CardHeader>
            <CardContent>
                <div v-if="attempts.data.length === 0" class="text-center py-12">
                    <FileText class="h-12 w-12 text-gray-400 mx-auto mb-4" />
                    <p class="text-gray-600">No quiz attempts yet</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Student</TableHead>
                                <TableHead>Quiz</TableHead>
                                <TableHead>Score</TableHead>
                                <TableHead>Correct</TableHead>
                                <TableHead>Duration</TableHead>
                                <TableHead>Date</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="attempt in attempts.data" :key="attempt.id">
                                <!-- Student Name -->
                                <TableCell>
                                    <div class="flex items-center gap-2">
                                        <User class="h-4 w-4 text-gray-400" />
                                        <div>
                                            <p class="font-medium text-gray-900">
                                                {{ attempt.student_name || 'Anonymous' }}
                                            </p>
                                            <p v-if="attempt.user" class="text-xs text-gray-500">
                                                {{ attempt.user.email }}
                                            </p>
                                        </div>
                                    </div>
                                </TableCell>

                                <!-- Quiz Title -->
                                <TableCell>
                                    <p class="font-medium text-gray-900">{{ attempt.quiz.title }}</p>
                                    <p class="text-xs text-gray-500">Passing: {{ attempt.quiz.passing_score }}%</p>
                                </TableCell>

                                <!-- Score -->
                                <TableCell>
                                    <div class="flex items-center gap-2">
                                        <Award :class="isPassed(attempt.score, attempt.quiz.passing_score) ? 'h-4 w-4 text-green-600' : 'h-4 w-4 text-red-600'" />
                                        <span class="font-bold text-lg" :class="isPassed(attempt.score, attempt.quiz.passing_score) ? 'text-green-700' : 'text-red-700'">
                                            {{ attempt.score }}%
                                        </span>
                                    </div>
                                </TableCell>

                                <!-- Correct Answers -->
                                <TableCell>
                                    <span class="font-medium text-gray-900">
                                        {{ attempt.correct_answers }}/{{ attempt.total_questions }}
                                    </span>
                                </TableCell>

                                <!-- Duration -->
                                <TableCell>
                                    <div class="flex items-center gap-1 text-gray-600">
                                        <Clock class="h-3 w-3" />
                                        <span class="text-sm">{{ formatDuration(attempt.started_at, attempt.completed_at) }}</span>
                                    </div>
                                </TableCell>

                                <!-- Date -->
                                <TableCell>
                                    <div class="flex items-center gap-1 text-gray-600">
                                        <Calendar class="h-3 w-3" />
                                        <span class="text-sm">{{ formatDate(attempt.completed_at) }}</span>
                                    </div>
                                </TableCell>

                                <!-- Status Badge -->
                                <TableCell>
                                    <Badge
                                        v-if="isPassed(attempt.score, attempt.quiz.passing_score)"
                                        class="bg-green-100 text-green-700 hover:bg-green-200"
                                    >
                                        <CheckCircle class="h-3 w-3 mr-1" />
                                        Passed
                                    </Badge>
                                    <Badge
                                        v-else
                                        class="bg-red-100 text-red-700 hover:bg-red-200"
                                    >
                                        <XCircle class="h-3 w-3 mr-1" />
                                        Failed
                                    </Badge>
                                </TableCell>

                                <!-- Actions -->
                                <TableCell class="text-right">
                                    <Link :href="`/admin/quiz-attempts/${attempt.id}`">
                                        <Button variant="outline" size="sm">
                                            View Details
                                        </Button>
                                    </Link>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <!-- Pagination -->
                <div v-if="attempts.last_page > 1" class="mt-6 flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Showing {{ (attempts.current_page - 1) * attempts.per_page + 1 }} to 
                        {{ Math.min(attempts.current_page * attempts.per_page, attempts.total) }} of 
                        {{ attempts.total }} results
                    </p>
                    <div class="flex gap-2">
                        <Link
                            v-if="attempts.current_page > 1"
                            :href="`/admin/quiz-attempts?page=${attempts.current_page - 1}`"
                        >
                            <Button variant="outline" size="sm">Previous</Button>
                        </Link>
                        <Link
                            v-if="attempts.current_page < attempts.last_page"
                            :href="`/admin/quiz-attempts?page=${attempts.current_page + 1}`"
                        >
                            <Button variant="outline" size="sm">Next</Button>
                        </Link>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
