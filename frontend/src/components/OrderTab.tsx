import type { Dispatch, SetStateAction } from 'react';
import type { OrderSettings } from '../types';
import { Checkbox, Field, TextInput } from './FormControls';

interface Props {
  order: OrderSettings;
  setOrder: Dispatch<SetStateAction<OrderSettings>>;
}

export function OrderTab({ order, setOrder }: Props) {
  function update<K extends keyof OrderSettings>(key: K, value: OrderSettings[K]) {
    setOrder((prev) => ({ ...prev, [key]: value }));
  }

  return (
    <div className="wralm-panel">
      <Checkbox
        id="enable_order"
        label="Enable Order"
        checked={order.enableOrder}
        onChange={(v) => update('enableOrder', v)}
      />

      {order.enableOrder && (
        <>
          <Field label="Label Newest" htmlFor="label_newest_order">
            <TextInput
              id="label_newest_order"
              value={order.labelNewestOrder}
              placeholder="Newest First"
              onChange={(v) => update('labelNewestOrder', v)}
            />
          </Field>

          <Field label="Label Old" htmlFor="label_old_order">
            <TextInput
              id="label_old_order"
              value={order.labelOldOrder}
              placeholder="Old First"
              onChange={(v) => update('labelOldOrder', v)}
            />
          </Field>
        </>
      )}
    </div>
  );
}
