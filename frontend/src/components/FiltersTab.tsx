import type { ChangeEvent, Dispatch, SetStateAction } from 'react';
import type { FilterSettings, FilterType } from '../types';
import { Checkbox, Field, Select, TextInput } from './FormControls';

interface Props {
  taxonomies: string[];
  filters: FilterSettings;
  setFilters: Dispatch<SetStateAction<FilterSettings>>;
}

const FILTER_TYPE_OPTIONS: { value: FilterType; label: string }[] = [
  { value: 'button', label: 'Button' },
  { value: 'select', label: 'Select' },
];

export function FiltersTab({ taxonomies, filters, setFilters }: Props) {
  function update<K extends keyof FilterSettings>(key: K, value: FilterSettings[K]) {
    setFilters((prev) => ({ ...prev, [key]: value }));
  }

  function handleTaxonomyChange(e: ChangeEvent<HTMLSelectElement>) {
    const selected = Array.from(e.target.selectedOptions).map((opt) => opt.value);
    update('filterTaxonomy', selected);
  }

  const allTaxonomies = ['category', 'post_tag', ...taxonomies.filter((t) => t !== 'category' && t !== 'post_tag')];

  return (
    <div className="wralm-panel">
      <Checkbox
        id="filter_by_category"
        label="Filter by category"
        checked={filters.filterByCategory}
        onChange={(v) => update('filterByCategory', v)}
      />

      {filters.filterByCategory && (
        <>
          <Field label="Filter by taxonomy" htmlFor="filter_taxonomy">
            <select
              id="filter_taxonomy"
              className="wralm-input"
              multiple
              value={filters.filterTaxonomy}
              onChange={handleTaxonomyChange}
            >
              {allTaxonomies.map((tax) => (
                <option key={tax} value={tax}>
                  {tax.charAt(0).toUpperCase() + tax.slice(1).replace(/[_-]/g, ' ')}
                </option>
              ))}
            </select>
          </Field>

          <Checkbox
            id="enable_clear_button"
            label="Enable Clear Button"
            checked={filters.enableClearButton}
            onChange={(v) => update('enableClearButton', v)}
          />

          <Field label="Filter row classes" htmlFor="filter_row_classes">
            <TextInput
              id="filter_row_classes"
              value={filters.filterRowClasses}
              placeholder="filter_row"
              onChange={(v) => update('filterRowClasses', v)}
            />
          </Field>

          <Field label="Filter Type" htmlFor="filter_type">
            <Select
              id="filter_type"
              value={filters.filterType}
              onChange={(v) => update('filterType', v as FilterType)}
              options={FILTER_TYPE_OPTIONS}
            />
          </Field>

          <Checkbox
            id="enable_filter_titles"
            label="Enable Filter Titles"
            checked={filters.enableFilterTitles}
            onChange={(v) => update('enableFilterTitles', v)}
          />

          <Checkbox
            id="multiply_filter"
            label="Enable Multiply Filter"
            checked={filters.multiplyFilter}
            onChange={(v) => update('multiplyFilter', v)}
          />

          <Field label="Filter item classes" htmlFor="filter_item_classes">
            <TextInput
              id="filter_item_classes"
              value={filters.filterItemClasses}
              placeholder="filter_item"
              onChange={(v) => update('filterItemClasses', v)}
            />
          </Field>

          <Field label="Label all category button" htmlFor="all_category_button">
            <TextInput
              id="all_category_button"
              value={filters.allCategoryButton}
              placeholder="All"
              onChange={(v) => update('allCategoryButton', v)}
            />
          </Field>
        </>
      )}
    </div>
  );
}
