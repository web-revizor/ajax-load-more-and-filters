import type { Dispatch, SetStateAction } from 'react';
import type { ClassSettings } from '../types';
import { Field, TextInput } from './FormControls';

interface Props {
  classes: ClassSettings;
  setClasses: Dispatch<SetStateAction<ClassSettings>>;
}

export function ClassesTab({ classes, setClasses }: Props) {
  function update<K extends keyof ClassSettings>(key: K, value: ClassSettings[K]) {
    setClasses((prev) => ({ ...prev, [key]: value }));
  }

  return (
    <div className="wralm-panel">
      <Field label="Row classes" htmlFor="row_classes">
        <TextInput
          id="row_classes"
          value={classes.rowClasses}
          placeholder="posts_row"
          onChange={(v) => update('rowClasses', v)}
        />
      </Field>
      <Field label="Load more button classes" htmlFor="load_more_classes">
        <TextInput
          id="load_more_classes"
          value={classes.loadMoreClasses}
          placeholder="load_more_button"
          onChange={(v) => update('loadMoreClasses', v)}
        />
      </Field>
    </div>
  );
}
