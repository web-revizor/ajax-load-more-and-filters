import type { Dispatch, SetStateAction } from 'react';
import type { MainSettings, PaginationType } from '../types';
import { Field, NumberInput, Select, TextInput } from './FormControls';

interface Props {
  postTypes: string[];
  main: MainSettings;
  setMain: Dispatch<SetStateAction<MainSettings>>;
}

const PAGINATION_OPTIONS: { value: PaginationType; label: string }[] = [
  { value: 'default', label: 'Only button' },
  { value: 'list', label: 'List' },
  { value: 'both', label: 'List + button' },
  { value: 'none', label: 'None' },
];

export function MainTab({ postTypes, main, setMain }: Props) {
  function update<K extends keyof MainSettings>(key: K, value: MainSettings[K]) {
    setMain((prev) => ({ ...prev, [key]: value }));
  }

  return (
    <div className="wralm-panel">
      <Field label="Post Type" htmlFor="post_type_load_more">
        <Select
          id="post_type_load_more"
          value={main.postType}
          onChange={(v) => update('postType', v)}
          options={postTypes.map((pt) => ({
            value: pt,
            label: pt.charAt(0).toUpperCase() + pt.slice(1),
          }))}
        />
      </Field>
      <Field label="Type Pagination" htmlFor="type_pagination">
        <Select
          id="type_pagination"
          value={main.typePagination}
          onChange={(v) => update('typePagination', v as PaginationType)}
          options={PAGINATION_OPTIONS}
        />
      </Field>
      <Field label="Count Post Per page" htmlFor="count_post_per_page">
        <NumberInput
          id="count_post_per_page"
          min={-1}
          value={main.postsPerPage}
          onChange={(v) => update('postsPerPage', v)}
        />
      </Field>
      <Field label="Load more label" htmlFor="load_more_label">
        <TextInput
          id="load_more_label"
          value={main.loadMoreLabel}
          placeholder="Load more"
          onChange={(v) => update('loadMoreLabel', v)}
        />
      </Field>
      <Field label="Previous label" htmlFor="prev_text">
        <TextInput
          id="prev_text"
          value={main.prevText}
          placeholder="Previous"
          onChange={(v) => update('prevText', v)}
        />
      </Field>
      <Field label="Next label" htmlFor="next_text">
        <TextInput
          id="next_text"
          value={main.nextText}
          placeholder="Next"
          onChange={(v) => update('nextText', v)}
        />
      </Field>
    </div>
  );
}
