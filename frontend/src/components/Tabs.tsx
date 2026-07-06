import type { TabId } from '../types';

interface TabDef {
  id: TabId;
  label: string;
}

const TABS: TabDef[] = [
  { id: 'main', label: 'Main' },
  { id: 'classes', label: 'Main classes' },
  { id: 'filters', label: 'Filters' },
  { id: 'search', label: 'Search' },
  { id: 'order', label: 'Order' },
];

interface Props {
  active: TabId;
  onChange: (tab: TabId) => void;
}

export function Tabs({ active, onChange }: Props) {
  return (
    <ul className="wralm-tabs" role="tablist">
      {TABS.map((tab) => (
        <li key={tab.id} role="presentation">
          <button
            type="button"
            role="tab"
            aria-selected={active === tab.id}
            className={`wralm-tab${active === tab.id ? ' is-active' : ''}`}
            onClick={() => onChange(tab.id)}
          >
            {tab.label}
          </button>
        </li>
      ))}
    </ul>
  );
}
