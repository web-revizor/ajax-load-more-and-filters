import { showToast } from '@/src/utils/toastEmitter';

type CopyTarget = (MouseEvent & { currentTarget: HTMLElement }) | string;
export function copyText(target?: CopyTarget, testToCopy?: string) {
  let text: string | undefined;

  if (target) {
    if (typeof target === 'string') {
      const element = document.querySelector(target) as HTMLElement | null;
      text = element?.innerText?.trim();
    } else {
      const el = target.currentTarget;
      text = el?.innerText?.trim();
    }
  } else {
    text = testToCopy;
  }

  if (!text) return;

  const fallbackCopy = () => {
    const textarea = document.createElement('textarea');
    textarea.value = text!;
    textarea.style.position = 'fixed';
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();
    try {
      document.execCommand('copy');
      showToast({ message: 'Copied via fallback!', type: 'success' });
    } catch {
      showToast({ message: 'Copy fallback failed!', type: 'error' });
    } finally {
      document.body.removeChild(textarea);
    }
  };

  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard
      .writeText(text)
      .then(
        () => showToast({ message: 'Copied!', type: 'success' }),
        fallbackCopy
      );
  } else {
    fallbackCopy();
  }
}
