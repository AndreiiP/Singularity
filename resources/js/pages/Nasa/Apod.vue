<script setup lang="ts">
import { computed } from 'vue';
import { useApod } from '@/composables/useApod';
import { Head } from '@inertiajs/vue3';

import { Card, CardHeader, CardTitle, CardContent, CardDescription } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";

const { data, loading, error, reload } = useApod(true);

const isImage = computed(() => data.value?.media_type === 'image');
const imageSrc = computed(() => data.value?.hdurl || data.value?.url || '');
</script>

<template>
  <Head title="NASA APOD" />

  <div class="mx-auto max-w-4xl p-6 space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight">NASA Astronomy Picture of the Day</h1>
      <Button @click="reload">Refresh</Button>
    </div>

    <Card>
      <CardHeader>
        <CardTitle>{{ data?.title || '—' }}</CardTitle>
        <CardDescription v-if="data?.date">Date: {{ data.date }}</CardDescription>
      </CardHeader>

      <CardContent class="space-y-4">
        <!-- Loading -->
        <div v-if="loading" class="space-y-3">
          <Skeleton class="h-6 w-2/3" />
          <Skeleton class="h-[420px] w-full" />
          <Skeleton class="h-24 w-full" />
        </div>

        <!-- Error -->
        <div v-else-if="error" class="text-red-600">
          {{ error }}
        </div>

        <!-- Content -->
        <div v-else-if="data" class="space-y-4">
          <div v-if="isImage" class="w-full">
            <img
              :src="imageSrc"
              :alt="data.title"
              class="w-full rounded-2xl shadow-sm"
              loading="lazy"
            />
          </div>

          <div v-else class="aspect-video w-full">
            <iframe
              v-if="data.url"
              :src="data.url"
              class="h-full w-full rounded-2xl"
              frameborder="0"
              allowfullscreen
            />
            <p v-else>Video is unavailable.</p>
          </div>

          <p class="text-sm leading-relaxed text-muted-foreground whitespace-pre-line">
            {{ data.explanation }}
          </p>

          <div v-if="data.copyright" class="text-xs text-muted-foreground">
            © {{ data.copyright }}
          </div>
        </div>

        <!-- Empty -->
        <div v-else>Empty.</div>
      </CardContent>
    </Card>
  </div>
</template>
