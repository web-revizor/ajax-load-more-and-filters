import { ToastType } from '@/src/components/sharedComponents/Toast/Toast';

export interface ToastEmitParams {
  message: string;
  type?: ToastType;
  duration?: number;
}

const TOAST_EVENT = 'wr:toast';

export function showToast(params: ToastEmitParams) {
  window.dispatchEvent(new CustomEvent(TOAST_EVENT, { detail: params }));
}

export function onToast(cb: (params: ToastEmitParams) => void) {
  const handler = (e: Event) => cb((e as CustomEvent<ToastEmitParams>).detail);
  window.addEventListener(TOAST_EVENT, handler);
  return () => window.removeEventListener(TOAST_EVENT, handler);
}
