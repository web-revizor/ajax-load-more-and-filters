'use client';
import React, { useState } from 'react';
import clsx from 'clsx';
import Input from '@/src/components/sharedComponents/Input/Input';
import ClickOutsideComponent from '@/src/components/sharedComponents/ClickOutsideComponent/ClickOutsideComponent';
import { Icon } from '@/src/components/sharedComponents/Icon';

interface ISelectOption {
  label: string;
  value: string;
  toolTipText?: string;
}

interface ISelect {
  label?: string;
  id?: string;
  placeholder?: string;
  className?: string;
  wrapperClassName?: string;
  required?: boolean;
  onChange?: (value: string | string[]) => void;
  onBlur?: () => void;
  value?: string | string[];
  name?: string;
  error?: boolean;
  errorMessage?: string;
  options: ISelectOption[];
  size?: 'default' | 'small';
  searchable?: boolean;
  position?: 'top' | 'bottom';
  dataTooltip?: string;
  multiple?: boolean;
}

export default function Select({
  placeholder,
  className,
  wrapperClassName,
  value,
  name,
  onChange,
  onBlur,
  error,
  errorMessage,
  options,
  label,
  id,
  size = 'default',
  searchable = false,
  position = 'bottom',
  dataTooltip,
  multiple = false,
}: ISelect) {
  const [open, setOpen] = useState(false);
  const [search, setSearch] = useState('');

  const selectedLabel = multiple
    ? options
        .filter((opt) => (value as string[] | undefined)?.includes(opt.value))
        .map((opt) => opt.label)
        .join(', ')
    : options.find((opt) => opt.value === value)?.label || '';

  const filteredOptions =
    searchable && search
      ? options.filter((opt) =>
          opt.label.toLowerCase().includes(search.toLowerCase())
        )
      : options;

  const handleSelect = (val: string) => {
    if (multiple) {
      const values = Array.isArray(value) ? value : [];

      onChange?.(
        values.includes(val)
          ? values.filter((v) => v !== val)
          : [...values, val]
      );

      return;
    }

    onChange?.(val);
    setOpen(false);
    setSearch('');
    onBlur?.();
  };

  return (
    <div className={clsx('relative group text-on-surface', wrapperClassName)}>
      {label && (
        <label
          htmlFor={id}
          onClick={() => {
            if (!open) setOpen(true);
          }}
          className='block text-label-bold mb-2'
        >
          {label}
        </label>
      )}

      <ClickOutsideComponent
        onOutsideClick={() => {
          if (open) setOpen(false);
        }}
      >
        <div
          {...(dataTooltip ? { 'data-tooltip': dataTooltip } : null)}
          tabIndex={0}
          onClick={() => setOpen((prev) => !prev)}
          className={clsx(
            'globalTransition ring-1 hover:border-primary bg-surface-container border-solid border rounded-xl text-on-surface cursor-pointer',
            size === 'default' && 'px-4 py-3',
            size === 'small' && 'px-2 py-1',
            className,
            open
              ? 'ring-primary border-primary'
              : 'border-outline-variant/30 ring-transparent'
          )}
        >
          {multiple ? (
            (Array.isArray(value) ? value : []).map((v) => (
              <input key={v} id={id} name={name} type='hidden' value={v} />
            ))
          ) : (
            <input id={id} name={name} type='hidden' value={value} />
          )}

          <div className='flex items-center justify-between'>
            <span
              className={clsx(
                'whitespace-nowrap overflow-ellipsis flex-1 overflow-hidden block',
                !(multiple
                  ? Array.isArray(value) && value.length > 0
                  : value) && 'opacity-50'
              )}
            >
              {selectedLabel || placeholder || 'Select'}
            </span>

            <Icon
              name={'common/select-icon'}
              width={20}
              height={20}
              className={clsx('globalTransition', open ? 'rotate-180' : '')}
            />
          </div>
        </div>

        <div
          className={clsx(
            'visibleTransition absolute left-0 z-50 w-full',
            position === 'bottom' && 'top-full mt-2',
            position === 'top' && 'bottom-full mb-2',
            open
              ? 'opacity-100 visible'
              : 'opacity-0 translate-y-2 pointer-events-none'
          )}
        >
          <div
            className={
              'flex flex-col shadow-select py-2 w-full bg-surface-container border border-solid border-outline-variant/30 rounded-2xl overflow-hidden'
            }
          >
            {searchable && (
              <div
                className={clsx(
                  'px-2',
                  position === 'top' && 'order-1 pt-2',
                  position === 'bottom' && 'pb-2'
                )}
              >
                <Input
                  placeholder='Search...'
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                  className={clsx(size === 'small' && '!px-2 !py-1 text-sm')}
                />
              </div>
            )}

            <div
              className={clsx(
                'flex-1 pl-1 mr-1 overflow-y-auto customScroll',
                size === 'default' && 'max-h-52',
                size === 'small' && 'max-h-24'
              )}
            >
              {filteredOptions.map((opt) => (
                <div
                  key={opt.value}
                  onClick={() => handleSelect(opt.value)}
                  className={clsx(
                    'cursor-pointer hover:bg-primary/10 overflow-hidden overflow-ellipsis whitespace-nowrap',
                    size === 'default' && 'px-4 py-2',
                    size === 'small' && 'px-2 py-1',
                    (multiple
                      ? (value as string[] | undefined)?.includes(opt.value)
                      : opt.value === value) && 'bg-primary/10'
                  )}
                  {...(opt.toolTipText
                    ? { 'data-tooltip': opt.toolTipText }
                    : null)}
                >
                  {opt.label}
                </div>
              ))}

              {filteredOptions.length === 0 && (
                <p
                  className={clsx(
                    'text-on-surface/50 text-sm',
                    size === 'default' && 'px-4 py-2',
                    size === 'small' && 'px-2 py-1'
                  )}
                >
                  No results
                </p>
              )}
            </div>
          </div>
        </div>
      </ClickOutsideComponent>

      {error && errorMessage && (
        <p className='absolute text-error text-[10px] left-0 -bottom-1 translate-y-full'>
          {errorMessage}
        </p>
      )}
    </div>
  );
}
