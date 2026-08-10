<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { BarChart, Eye, FileText, TrendingUp, Users } from '@lucide/vue';
import { computed } from 'vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { dashboard } from '@/routes';
import LineChart from '@/components/charts/LineChart.vue';
import BarChartComponent from '@/components/charts/BarChartComponent.vue';

defineOptions({
    layout: AppLayout,
});

interface Stats {
    total_presentations: number;
    total_users: number;
    user_presentations: number;
    total_views: number;
}

interface RecentPresentation {
    id: number;
    title: string;
    uploaded_by: string;
    created_at: string;
    views_count: number;
}

interface PresentationByMonth {
    month: string;
    count: number;
}

interface TopUploader {
    name: string;
    presentations_count: number;
}

interface Props {
    stats: Stats;
    recentPresentations: RecentPresentation[];
    presentationsByMonth: PresentationByMonth[];
    topUploaders: TopUploader[];
}

const props = defineProps<Props>();

const chartData = computed(() => {
    return {
        labels: props.presentationsByMonth.map((item) =>
            new Date(item.month + '-01').toLocaleDateString('en-US', {
                month: 'short',
                year: 'numeric',
            })
        ),
        datasets: [
            {
                label: 'Presentations',
                data: props.presentationsByMonth.map((item) => item.count),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
            },
        ],
    };
});

const topUploadersData = computed(() => {
    return {
        labels: props.topUploaders.map((user) => user.name),
        datasets: [
            {
                label: 'Presentations',
                data: props.topUploaders.map((user) => user.presentations_count),
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(251, 146, 60, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                ],
            },
        ],
    };
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-4 sm:gap-6 overflow-x-hidden p-4 sm:p-6">
        <!-- Stats Grid (responsive) -->
        <div class="grid gap-3 sm:gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Presentations (responsive) -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-xs sm:text-sm font-medium">
                        Total Presentations
                    </CardTitle>
                    <FileText class="h-4 w-4 sm:h-5 sm:w-5 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-xl sm:text-2xl font-bold">
                        {{ stats.total_presentations }}
                    </div>
                    <p class="text-xs sm:text-sm text-muted-foreground">
                        All presentations in the system
                    </p>
                </CardContent>
            </Card>

            <!-- Total Users (responsive) -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-xs sm:text-sm font-medium">
                        Total Users
                    </CardTitle>
                    <Users class="h-4 w-4 sm:h-5 sm:w-5 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-xl sm:text-2xl font-bold">
                        {{ stats.total_users }}
                    </div>
                    <p class="text-xs sm:text-sm text-muted-foreground">
                        Registered users
                    </p>
                </CardContent>
            </Card>

            <!-- Your Presentations (responsive) -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-xs sm:text-sm font-medium">
                        Your Presentations
                    </CardTitle>
                    <TrendingUp class="h-4 w-4 sm:h-5 sm:w-5 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-xl sm:text-2xl font-bold">
                        {{ stats.user_presentations }}
                    </div>
                    <p class="text-xs sm:text-sm text-muted-foreground">
                        Presentations you've uploaded
                    </p>
                </CardContent>
            </Card>

            <!-- Total Views (responsive) -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-xs sm:text-sm font-medium">
                        Total Views
                    </CardTitle>
                    <Eye class="h-4 w-4 sm:h-5 sm:w-5 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-xl sm:text-2xl font-bold">
                        {{ stats.total_views }}
                    </div>
                    <p class="text-xs sm:text-sm text-muted-foreground">
                        Across all presentations
                    </p>
                </CardContent>
            </Card>
        </div>

        <!-- Charts Row (responsive) -->
        <div class="grid gap-3 sm:gap-4 grid-cols-1 lg:grid-cols-2">
            <!-- Presentations Over Time (responsive chart) -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-base sm:text-lg">Presentations Over Time</CardTitle>
                    <CardDescription class="text-xs sm:text-sm">
                        Presentations uploaded in the last 6 months
                    </CardDescription>
                </CardHeader>
                <CardContent class="px-2 sm:px-6">
                    <div class="w-full overflow-hidden">
                        <LineChart :data="chartData" />
                    </div>
                </CardContent>
            </Card>

            <!-- Top Uploaders (responsive chart) -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-base sm:text-lg">Top Uploaders</CardTitle>
                    <CardDescription class="text-xs sm:text-sm">
                        Users with the most presentations
                    </CardDescription>
                </CardHeader>
                <CardContent class="px-2 sm:px-6">
                    <div class="w-full overflow-hidden">
                        <BarChartComponent :data="topUploadersData" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Recent Presentations Table (responsive) -->
        <Card>
            <CardHeader>
                <CardTitle class="text-base sm:text-lg">Recent Presentations</CardTitle>
                <CardDescription class="text-xs sm:text-sm">
                    The latest presentations uploaded to the platform
                </CardDescription>
            </CardHeader>
            <CardContent class="px-2 sm:px-6">
                <div class="space-y-3 sm:space-y-4">
                    <div
                        v-for="presentation in recentPresentations"
                        :key="presentation.id"
                        class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 rounded-lg border p-3 sm:p-4"
                    >
                        <div class="flex-1 min-w-0 w-full sm:w-auto">
                            <h3 class="font-semibold text-sm sm:text-base line-clamp-2">{{ presentation.title }}</h3>
                            <p class="text-xs sm:text-sm text-muted-foreground mt-1">
                                Uploaded by {{ presentation.uploaded_by }} •
                                {{ presentation.created_at }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 text-xs sm:text-sm text-muted-foreground self-end sm:self-center">
                            <Eye class="h-3.5 w-3.5 sm:h-4 sm:w-4" />
                            <span>{{ presentation.views_count }}</span>
                        </div>
                    </div>
                    <div
                        v-if="recentPresentations.length === 0"
                        class="py-8 sm:py-12 text-center text-sm sm:text-base text-muted-foreground"
                    >
                        No presentations yet. Upload your first presentation to get
                        started!
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
