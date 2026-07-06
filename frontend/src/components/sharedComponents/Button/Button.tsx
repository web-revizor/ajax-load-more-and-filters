import clsx from 'clsx';
import React, {HTMLAttributeAnchorTarget, ReactNode} from 'react';
import {Icon} from '@/src/components/sharedComponents/Icon';

interface IButton {
    title?: string;
    type?: 'submit' | 'reset' | 'button';
    onClick?: (
        e: React.MouseEvent<HTMLAnchorElement | HTMLButtonElement>
    ) => void;
    className?: string;
    id?: string;
    size?: IButtonSize;
    href?: string;
    target?: HTMLAttributeAnchorTarget | undefined;
    disabled?: boolean;
    variant?: IButtonVariant;
    nofollow?: boolean;
    arrow_icon?: boolean;
    onMouseDown?: (e: React.MouseEvent<HTMLButtonElement>) => void;
    children?: ReactNode;
    dataTooltip?: string;
}

export type IButtonVariant = 'primary' | 'secondary' | 'clear' | 'chat';
export type IButtonSize = 'default' | 'smaller' | 'small' | 'large' | 'clear';

export default function Button({
                                   type = 'button',
                                   title,
                                   onClick,
                                   className,
                                   id,
                                   size = 'default',
                                   href,
                                   target,
                                   disabled,
                                   variant = 'primary',
                                   nofollow,
                                   arrow_icon,
                                   children,
                                   onMouseDown,
                                   dataTooltip,
                               }: IButton) {
    const commonClasses = clsx(
        'text-on-surface font-button rounded-full text-button text-center flex items-center justify-center',
        className,
        size === 'default' && 'px-8 py-4 gap-2',
        size === 'small' && 'px-7 py-4 gap-2',
        size === 'smaller' && 'px-4 py-2 gap-1',
        size === 'large' && '425:px-12 425:py-6 px-8 py-4 uppercase gap-4',
        (variant === 'primary' || variant === 'chat') &&
        'bg-primary-container border border-solid border-bg-primary-container hover:border-on-primary-fixed-variant hover:bg-on-primary-fixed-variant magenta-glow disabled:!bg-white/20 disabled:!cursor-default disabled:!shadow-none',
        variant === 'secondary' && 'glass-card hover:!bg-primary/10 text-primary',
        variant === 'clear' && 'p-0 gap-1',
        variant === 'chat' &&
        'p-0 flex-grow-0 flex-shrink-0 aspect-square !rounded-xl',
        arrow_icon && size === 'large' && 'group overflow-hidden'
    );
    const iconSizes = size === 'large' ? 24 : 20;

    return (
        <button
            id={id}
            type={type}
            onClick={onClick}
            onMouseDown={onMouseDown}
            className={commonClasses}
            disabled={disabled}
            {...(dataTooltip ? {'data-tooltip': dataTooltip} : null)}
        >
            {children ?? title}
            {arrow_icon ? (
                <Icon
                    className={'group-hover:animate-button-icon'}
                    name={'common/send'}
                    width={iconSizes}
                    height={iconSizes}
                />
            ) : null}
        </button>
    );
}
