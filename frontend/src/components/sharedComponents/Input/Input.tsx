import React, { CSSProperties, forwardRef, useEffect, useRef } from 'react';
import clsx from 'clsx';

interface IInput {
  type?: React.InputHTMLAttributes<HTMLInputElement>['type'];
  id?: string;
  label?: string;
  hiddenLabel?: boolean;
  placeholder?: string;
  className?: string;
  wrapperClassName?: string;
  required?: boolean;
  onChange?: React.ChangeEventHandler<HTMLInputElement | HTMLTextAreaElement>;
  onKeyDown?: (
    e: React.KeyboardEvent<HTMLInputElement | HTMLTextAreaElement>
  ) => void;
  value?: string;
  name?: string;
  title?: string;
  onBlur?: React.FocusEventHandler<HTMLInputElement | HTMLTextAreaElement>;
  textarea?: boolean;
  error?: boolean;
  errorMessage?: string;
  rows?: number;
  onKeyPress?: React.KeyboardEventHandler<
    HTMLInputElement | HTMLTextAreaElement
  >;
  style?: CSSProperties | undefined;
  min?: number;
  max?: number;
  step?: number;
  autofocus?: boolean;
  onInput?: React.FormEventHandler<HTMLInputElement | HTMLTextAreaElement>;
  readOnly?: boolean;
  onClick?: () => void;
}

const Input = forwardRef<HTMLInputElement | HTMLTextAreaElement, IInput>(
  (
    {
      required,
      placeholder,
      className,
      wrapperClassName,
      type,
      id,
      value,
      onChange,
      onKeyDown,
      name,
      title,
      onBlur,
      onInput,
      textarea,
      error,
      errorMessage,
      rows = 10,
      onKeyPress,
      label,
      hiddenLabel,
      style,
      min,
      max,
      step,
      autofocus,
      readOnly,
      onClick,
    },
    ref
  ) => {
    const defaultClassName =
      'globalTransition px-4 py-3 bg-surface-container border border-solid border-outline-variant/30 rounded-xl focus-within:ring focus-within:ring-1 focus-within:ring-primary focus-within:border-primary hover:border-primary !text-on-surface';
    const resetClassName =
      'p-0  h-full w-full bg-transparent shadow-none focus:outline-none outline-none !ring-0 !border-none overflow-auto customScroll overscroll-none !text-current !appearance-none !min-h-0';
    const inputRef = useRef<HTMLInputElement | HTMLTextAreaElement | null>(
      null
    );

    const setRefs = (
      element: HTMLInputElement | HTMLTextAreaElement | null
    ) => {
      inputRef.current = element;

      if (!ref) {
        return;
      }

      if (typeof ref === 'function') {
        ref(element);
      } else {
        ref.current = element;
      }
    };

    useEffect(() => {
      if (error && inputRef.current) {
        inputRef.current.focus();
      }
    }, [error]);

    return (
      <div className={clsx('relative group', wrapperClassName)}>
        {label ? (
          <label
            htmlFor={id}
            className={clsx(
              'block text-label-bold mb-2',
              hiddenLabel && 'sr-only'
            )}
          >
            {label}
          </label>
        ) : null}

        <label
          className={clsx(
            'block',
            defaultClassName,
            className,
            textarea && 'h-32 pr-3'
          )}
        >
          {textarea ? (
            <textarea
              style={style}
              id={id}
              ref={setRefs}
              className={clsx(resetClassName, 'pr-1 resize-none')}
              placeholder={placeholder}
              required={required}
              onChange={onChange}
              onKeyDown={onKeyDown}
              onBlur={onBlur}
              onInput={onInput}
              value={value}
              name={name}
              title={title}
              rows={rows}
              autoFocus={autofocus}
              readOnly={readOnly}
              onClick={onClick}
            />
          ) : (
            <input
              style={style}
              id={id}
              ref={setRefs}
              className={clsx(resetClassName)}
              name={name}
              title={title}
              type={type}
              value={value}
              placeholder={placeholder}
              required={required}
              onChange={onChange as React.ChangeEventHandler<HTMLInputElement>}
              onKeyDown={
                onKeyDown as React.KeyboardEventHandler<HTMLInputElement>
              }
              onBlur={onBlur as React.FocusEventHandler<HTMLInputElement>}
              onInput={onInput as React.FormEventHandler<HTMLInputElement>}
              onKeyPress={onKeyPress}
              min={min}
              max={max}
              step={step}
              autoFocus={autofocus}
              readOnly={readOnly}
              onClick={onClick}
            />
          )}
        </label>

        {error && errorMessage && (
          <p className='absolute text-error text-[10px] left-0 -bottom-1 translate-y-full'>
            {errorMessage}
          </p>
        )}
      </div>
    );
  }
);

Input.displayName = 'Input';

export default Input;
