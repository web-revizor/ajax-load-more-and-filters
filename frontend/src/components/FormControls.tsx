import type { ChangeEvent, ReactNode } from 'react';

interface FieldProps {
  label: string;
  htmlFor: string;
  children: ReactNode;
}

export function Field({ label, htmlFor, children }: FieldProps) {
  return (
    <div className="wralm-field">
      <label htmlFor={htmlFor}>{label}</label>
      {children}
    </div>
  );
}

interface TextInputProps {
  id: string;
  value: string;
  placeholder?: string;
  onChange: (value: string) => void;
}

export function TextInput({ id, value, placeholder, onChange }: TextInputProps) {
  return (
    <input
      type="text"
      id={id}
      className="wralm-input"
      value={value}
      placeholder={placeholder}
      onChange={(e: ChangeEvent<HTMLInputElement>) => onChange(e.target.value)}
    />
  );
}

interface NumberInputProps {
  id: string;
  value: number;
  min?: number;
  onChange: (value: number) => void;
}

export function NumberInput({ id, value, min, onChange }: NumberInputProps) {
  return (
    <input
      type="number"
      id={id}
      min={min}
      className="wralm-input"
      value={value}
      onChange={(e: ChangeEvent<HTMLInputElement>) => onChange(Number(e.target.value))}
    />
  );
}

interface CheckboxProps {
  id: string;
  label: string;
  checked: boolean;
  onChange: (value: boolean) => void;
}

export function Checkbox({ id, label, checked, onChange }: CheckboxProps) {
  return (
    <div className="wralm-field">
      <label htmlFor={id} className="wralm-checkbox-label">
        {label}
      </label>
      <input
        type="checkbox"
        id={id}
        checked={checked}
        onChange={(e: ChangeEvent<HTMLInputElement>) => onChange(e.target.checked)}
      />
    </div>
  );
}

interface SelectOption {
  value: string;
  label: string;
}

interface SelectProps {
  id: string;
  value: string;
  options: SelectOption[];
  onChange: (value: string) => void;
}

export function Select({ id, value, options, onChange }: SelectProps) {
  return (
    <select
      id={id}
      className="wralm-input"
      value={value}
      onChange={(e: ChangeEvent<HTMLSelectElement>) => onChange(e.target.value)}
    >
      {options.map((opt) => (
        <option key={opt.value} value={opt.value}>
          {opt.label}
        </option>
      ))}
    </select>
  );
}
