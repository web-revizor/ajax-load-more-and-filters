import {useState} from 'react';
import {copyToClipboard} from '@/src/utils/clipboard';

interface Props {
    value: string;
}

export function ShortcodeField({value}: Props) {
    const [copied, setCopied] = useState(false);

    async function handleCopy() {
        const ok = await copyToClipboard(value);
        if (ok) {
            setCopied(true);
            setTimeout(() => setCopied(false), 1000);
        }
    }

    return (
        <div className="wralm-shortcode-field">
            <input
                type="text"
                readOnly
                className="wralm-input wralm-shortcode-input"
                value={value}
                title="Copy"
                onClick={handleCopy}
            />
            <button type="button"
                    className="wralm-copy-btn"
                    title="Copy"
                    onClick={handleCopy}>
                {copied ? (
                    <svg width="22"
                         height="22"
                         viewBox="0 0 24 24"
                         fill="none">
                        <path
                            d="M4 12.611 8.923 17.5 20 6.5"
                            stroke="#00ff40"
                            strokeWidth="2"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                        />
                    </svg>
                ) : (
                    <svg width="22"
                         height="22"
                         viewBox="0 0 512 512">
                        <path
                            fill="currentColor"
                            d="M224 0c-35.3 0-64 28.7-64 64V288c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64H224zM64 160c-35.3 0-64 28.7-64 64V448c0 35.3 28.7 64 64 64H288c35.3 0 64-28.7 64-64V384H288v64H64V224h64V160H64z"
                        />
                    </svg>
                )}
            </button>
        </div>
    );
}
