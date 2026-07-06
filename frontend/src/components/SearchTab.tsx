import type { Dispatch, SetStateAction } from 'react';
import type { SearchSettings } from '../types';
import { Checkbox, Field, TextInput } from './FormControls';

interface Props {
  search: SearchSettings;
  setSearch: Dispatch<SetStateAction<SearchSettings>>;
}

export function SearchTab({ search, setSearch }: Props) {
  function update<K extends keyof SearchSettings>(key: K, value: SearchSettings[K]) {
    setSearch((prev) => ({ ...prev, [key]: value }));
  }

  return (
    <div className="wralm-panel">
      <Checkbox
        id="enable_search"
        label="Enable Search"
        checked={search.enableSearch}
        onChange={(v) => update('enableSearch', v)}
      />

      {search.enableSearch && (
        <>
          <Field label="Label search button" htmlFor="label_search_button">
            <TextInput
              id="label_search_button"
              value={search.labelSearchButton}
              placeholder="Search"
              onChange={(v) => update('labelSearchButton', v)}
            />
          </Field>

          <Field label="Search Placeholder" htmlFor="search_placeholder">
            <TextInput
              id="search_placeholder"
              value={search.searchPlaceholder}
              placeholder="Search"
              onChange={(v) => update('searchPlaceholder', v)}
            />
          </Field>
        </>
      )}
    </div>
  );
}
