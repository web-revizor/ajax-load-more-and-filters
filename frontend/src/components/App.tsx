import { useState } from 'react';
import type { TabId, WralmSettings } from '../types';
import { useShortcodeBuilder } from '../hooks/useShortcodeBuilder';
import { Tabs } from './Tabs';
import { MainTab } from './MainTab';
import { ClassesTab } from './ClassesTab';
import { FiltersTab } from './FiltersTab';
import { SearchTab } from './SearchTab';
import { OrderTab } from './OrderTab';
import { ShortcodeField } from './ShortcodeField';

interface Props {
  settings: WralmSettings;
}

export function App({ settings }: Props) {
  const [tab, setTab] = useState<TabId>('main');
  const {
    state,
    setMain,
    setClasses,
    setFilters,
    setSearch,
    setOrder,
    postsShortcode,
    filtersShortcode,
    hasFilters,
  } = useShortcodeBuilder();

  return (
    <div className="wralm-app">
      <h1 className="wralm-title">All Post AJAX</h1>

      <p className="wralm-hint">
        Edit a post-card.php in &quot;/all_posts_ajax/&quot; or create new {'{post-name}'}-card.php
      </p>

      <div className="wralm-layout">
        <Tabs active={tab} onChange={setTab} />

        <div className="wralm-panels">
          {tab === 'main' && (
            <MainTab postTypes={settings.postTypes} main={state.main} setMain={setMain} />
          )}
          {tab === 'classes' && <ClassesTab classes={state.classes} setClasses={setClasses} />}
          {tab === 'filters' && (
            <FiltersTab
              taxonomies={settings.taxonomies}
              filters={state.filters}
              setFilters={setFilters}
            />
          )}
          {tab === 'search' && <SearchTab search={state.search} setSearch={setSearch} />}
          {tab === 'order' && <OrderTab order={state.order} setOrder={setOrder} />}
        </div>
      </div>

      <ShortcodeField value={postsShortcode} />

      {hasFilters && <ShortcodeField value={filtersShortcode} />}
    </div>
  );
}
