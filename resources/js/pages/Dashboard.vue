<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
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

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

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

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-6">
        <!-- Stats Grid -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Total Presentations -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">
                        Total Presentations
                    </CardTitle>
                    <FileText class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">
                        {{ stats.total_presentations }}
                    </div>
                    <p class="text-xs text-muted-foreground">
                        All presentations in the system
                    </p>
                </CardContent>
            </Card>

            <!-- Total Users -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">
                        Total Users
                    </CardTitle>
                    <Users class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">
                        {{ stats.total_users }}
                    </div>
                    <p class="text-xs text-muted-foreground">
                        Registered users
                    </p>
                </CardContent>
            </Card>

            <!-- Your Presentations -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">
                        Your Presentations
                    </CardTitle>
                    <TrendingUp class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">
                        {{ stats.user_presentations }}
                    </div>
                    <p class="text-xs text-muted-foreground">
                        Presentations you've uploaded
                    </p>
                </CardContent>
            </Card>

            <!-- Total Views -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">
                        Total Views
                    </CardTitle>
                    <Eye class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">
                        {{ stats.total_views }}
                    </div>
                    <p class="text-xs text-muted-foreground">
                        Across all presentations
                    </p>
                </CardContent>
            </Card>
        </div>

        <!-- Charts Row -->
        <div class="grid gap-4 md:grid-cols-2">
            <!-- Presentations Over Time -->
            <Card>
                <CardHeader>
                    <CardTitle>Presentations Over Time</CardTitle>
                    <CardDescription>
                        Presentations uploaded in the last 6 months
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <LineChart :data="chartData" />
                </CardContent>
            </Card>

            <!-- Top Uploaders -->
            <Card>
                <CardHeader>
                    <CardTitle>Top Uploaders</CardTitle>
                    <CardDescription>
                        Users with the most presentations
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <BarChartComponent :data="topUploadersData" />
                </CardContent>
            </Card>
        </div>

        <!-- Recent Presentations Table -->
        <Card>
            <CardHeader>
                <CardTitle>Recent Presentations</CardTitle>
                <CardDescription>
                    The latest presentations uploaded to the platform
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="space-y-4">
                    <div
                        v-for="presentation in recentPresentations"
                        :key="presentation.id"
                        class="flex items-center justify-between rounded-lg border p-4"
                    >
                        <div class="flex-1">
                            <h3 class="font-semibold">{{ presentation.title }}</h3>
                            <p class="text-sm text-muted-foreground">
                                Uploaded by {{ presentation.uploaded_by }} •
                                {{ presentation.created_at }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                            <Eye class="h-4 w-4" />
                            <span>{{ presentation.views_count }}</span>
                        </div>
                    </div>
                    <div
                        v-if="recentPresentations.length === 0"
                        class="py-8 text-center text-muted-foreground"
                    >
                        No presentations yet. Upload your first presentation to get
                        started!
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
