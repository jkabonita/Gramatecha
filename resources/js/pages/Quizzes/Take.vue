<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { BookOpen, Clock, CheckCircle } from '@lucide/vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { toast } from 'vue-sonner';

interface QuizQuestion {
    id: number;
    question: string;
    options: Record<string, string>;
    order: number;
}

interface Quiz {
    id: number;
    title: string;
    slug: string;
    time_limit: number | null;
    questions: QuizQuestion[];
}

const props = defineProps<{
    quiz: Quiz;
    studentName: string;
}>();

defineOptions({
    layout: PublicLayout,
});

const answers = ref<Record<number, string>>({});
const currentQuestion = ref(0);
const startedAt = ref<string>(new Date().toISOString());
const timeRemaining = ref<number | null>(props.quiz.time_limit ? props.quiz.time_limit * 60 : null);
const isSubmitting = ref(false);

let timerInterval: ReturnType<typeof setInterval> | null = null;

const currentQuestionData = computed(() => props.quiz.questions[currentQuestion.value]);
const totalQuestions = computed(() => props.quiz.questions.length);
const answeredQuestions = computed(() => Object.keys(answers.value).length);
const progress = computed(() => Math.round((answeredQuestions.value / totalQuestions.value) * 100));

const formatTime = (seconds: number) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

const nextQuestion = () => {
    if (currentQuestion.value < totalQuestions.value - 1) {
        currentQuestion.value++;
    }
};

const previousQuestion = () => {
    if (currentQuestion.value > 0) {
        currentQuestion.value--;
    }
};

const goToQuestion = (index: number) => {
    currentQuestion.value = index;
};

const submitQuiz = () => {
    if (answeredQuestions.value < totalQuestions.value) {
        const unanswered = totalQuestions.value - answeredQuestions.value;
        if (!confirm(`You have ${unanswered} unanswered question(s). Do you want to submit anyway?`)) {
            return;
        }
    }

    isSubmitting.value = true;

    router.post(`/quizzes/${props.quiz.slug}/submit`, {
        answers: answers.value,
        started_at: startedAt.value,
        student_name: props.studentName,
    }, {
        onError: () => {
            isSubmitting.value = false;
            toast.error('Failed to submit quiz. Please try again.');
        },
    });
};

onMounted(() => {
    if (timeRemaining.value !== null) {
        timerInterval = setInterval(() => {
            if (timeRemaining.value !== null && timeRemaining.value > 0) {
                timeRemaining.value--;
                
                if (timeRemaining.value === 60) {
                    toast.warning('1 minute remaining!');
                }
                
                if (timeRemaining.value === 0) {
                    toast.error('Time is up! Submitting quiz...');
                    submitQuiz();
                }
            }
        }, 1000);
    }
});

onUnmounted(() => {
    if (timerInterval) {
        clearInterval(timerInterval);
    }
});
</script>

<template>
    <Head :title="`Taking: ${quiz.title}`" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-4 sm:py-6 lg:py-8">
        <div class="container mx-auto px-3 sm:px-4">
            <!-- Header (responsive) -->
            <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900">{{ quiz.title }}</h1>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">Question {{ currentQuestion + 1 }} of {{ totalQuestions }}</p>
                </div>
                <div v-if="timeRemaining !== null" class="flex items-center gap-2 px-3 sm:px-4 py-2 rounded-lg bg-white border shadow-sm">
                    <Clock :class="timeRemaining < 60 ? 'h-4 w-4 sm:h-5 sm:w-5 text-red-600' : 'h-4 w-4 sm:h-5 sm:w-5 text-blue-600'" />
                    <span :class="timeRemaining < 60 ? 'font-bold text-sm sm:text-base text-red-600' : 'font-semibold text-sm sm:text-base text-gray-900'">
                        {{ formatTime(timeRemaining) }}
                    </span>
                </div>
            </div>

            <div class="grid gap-4 sm:gap-6 lg:grid-cols-4">
                <!-- Question Navigation Sidebar (responsive) -->
                <div class="lg:col-span-1 order-2 lg:order-1">
                    <Card class="lg:sticky lg:top-4">
                        <CardHeader class="pb-3 sm:pb-4">
                            <CardTitle class="text-sm sm:text-base">Questions</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-5 sm:grid-cols-6 lg:grid-cols-4 gap-2">
                                <button
                                    v-for="(question, index) in quiz.questions"
                                    :key="question.id"
                                    @click="goToQuestion(index)"
                                    type="button"
                                    :class="[
                                        'aspect-square rounded-lg font-semibold text-xs sm:text-sm transition-all touch-manipulation',
                                        currentQuestion === index
                                            ? 'bg-blue-600 text-white ring-2 ring-blue-400 ring-offset-2 scale-105'
                                            : answers[question.id]
                                            ? 'bg-green-500 text-white hover:bg-green-600 active:bg-green-700 hover:scale-105'
                                            : 'bg-gray-200 text-gray-700 hover:bg-gray-300 active:bg-gray-400 hover:scale-105'
                                    ]"
                                >
                                    {{ index + 1 }}
                                </button>
                            </div>

                            <div class="mt-4 pt-4 border-t space-y-2">
                                <div class="flex items-center justify-between text-xs sm:text-sm">
                                    <span class="text-gray-600">Answered:</span>
                                    <span class="font-semibold text-gray-900">{{ answeredQuestions }}/{{ totalQuestions }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 sm:h-3 overflow-hidden">
                                    <div class="bg-gradient-to-r from-green-500 to-green-600 h-full rounded-full transition-all duration-300" :style="{ width: `${progress}%` }"></div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Question Content (responsive) -->
                <div class="lg:col-span-3 order-1 lg:order-2">
                    <Card>
                        <CardHeader>
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 rounded-full bg-blue-100 p-2 sm:p-2.5">
                                    <BookOpen class="h-4 w-4 sm:h-5 sm:w-5 text-blue-600" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs sm:text-sm text-gray-600 mb-2">Question {{ currentQuestion + 1 }}</p>
                                    <CardTitle class="text-base sm:text-lg lg:text-xl leading-relaxed">
                                        {{ currentQuestionData.question }}
                                    </CardTitle>
                                </div>
                            </div>
                        </CardHeader>

                        <CardContent class="space-y-3 sm:space-y-4">
                            <RadioGroup v-model="answers[currentQuestionData.id]">
                                <div
                                    v-for="(option, key) in currentQuestionData.options"
                                    :key="key"
                                    @click="answers[currentQuestionData.id] = key"
                                    class="flex items-start gap-2 sm:gap-3 p-3 sm:p-4 rounded-lg border-2 transition-all cursor-pointer hover:border-blue-400 hover:bg-blue-50 active:scale-[0.99] touch-manipulation"
                                    :class="answers[currentQuestionData.id] === key ? 'border-blue-600 bg-blue-100 shadow-sm' : 'border-gray-300 bg-white'"
                                >
                                    <RadioGroupItem :value="key" :id="`option-${key}`" class="mt-0.5 pointer-events-none" />
                                    <Label :for="`option-${key}`" class="flex-1 cursor-pointer pointer-events-none select-none text-sm sm:text-base">
                                        <span class="font-semibold text-gray-800">{{ key.toUpperCase() }}.</span>
                                        <span class="ml-2 text-gray-900">{{ option }}</span>
                                    </Label>
                                    <CheckCircle v-if="answers[currentQuestionData.id] === key" class="h-4 w-4 sm:h-5 sm:w-5 text-blue-600 shrink-0" />
                                </div>
                            </RadioGroup>

                            <!-- Navigation Buttons (responsive) -->
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-6 border-t">
                                <Button
                                    @click="previousQuestion"
                                    :disabled="currentQuestion === 0"
                                    variant="outline"
                                    size="lg"
                                    class="w-full sm:w-auto px-6 sm:px-8"
                                >
                                    Previous
                                </Button>

                                <div class="flex gap-3 w-full sm:w-auto">
                                    <Button
                                        v-if="currentQuestion < totalQuestions - 1"
                                        @click="nextQuestion"
                                        size="lg"
                                        class="flex-1 sm:flex-none px-6 sm:px-8 bg-blue-600 hover:bg-blue-700"
                                    >
                                        Next
                                    </Button>

                                    <Button
                                        v-else
                                        @click="submitQuiz"
                                        :disabled="isSubmitting"
                                        size="lg"
                                        class="flex-1 sm:flex-none px-6 sm:px-8 bg-green-600 hover:bg-green-700 text-white"
                                    >
                                        {{ isSubmitting ? 'Submitting...' : 'Submit Quiz' }}
                                    </Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>
