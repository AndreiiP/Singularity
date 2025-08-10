import { ref, onMounted } from 'vue';
import type { NasaApod } from '@/types/nasa';
import axios from 'axios';

export function useApod(autoLoad = true) {
  const data = ref<NasaApod | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);
  let controller: AbortController | null = null;

  const load = async () => {
    error.value = null;
    loading.value = true;
    controller?.abort();
    controller = new AbortController();

    try {
      const res = await axios.get<NasaApod>('/api/nasa/apod', { signal: controller.signal });
      data.value = res.data;
    } catch (e: any) {
      if (e.name !== 'CanceledError' && e.message !== 'canceled') {
        error.value = e?.response?.data?.message || e?.message || 'Failed to load APOD';
      }
    } finally {
      loading.value = false;
    }
  };

  onMounted(() => {
    if (autoLoad) load();
  });

  return { data, loading, error, reload: load };
}
