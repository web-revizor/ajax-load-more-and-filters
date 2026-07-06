import { useState } from 'react';
import { WralmSettings } from '@/src/types/global';
import { TabId } from '@/src/types';
import { useShortcodeBuilder } from '@/src/hooks/useShortcodeBuilder';
import { Tabs } from '@/src/components/Tabs/Tabs';
import { MainTab } from '@/src/components/Tabs/MainTab';
import { FiltersTab } from '@/src/components/Tabs/FiltersTab';
import { ClassesTab } from '@/src/components/Tabs/ClassesTab';
import { SearchTab } from '@/src/components/Tabs/SearchTab';
import { OrderTab } from '@/src/components/Tabs/OrderTab';
import { ShortcodeField } from '@/src/components/ShortcodeField/ShortcodeField';

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
    <div className={'space-y-5 min-h-[50vh] relative max-w-6xl'}>
      <h1>All Post AJAX</h1>

      <div className='glass-card p-5 rounded-2xl'>
        Edit a post-card.php in &quot;/all_posts_ajax/&quot; or create new{' '}
        {'{post-name}'}-card.php
      </div>

      <div className='flex gap-5'>
        <Tabs active={tab} onChange={setTab} />

        <div className={'flex-1'}>
          <div className='glass p-5 rounded-2xl'>
            {tab === 'main' && (
              <MainTab
                postTypes={settings.postTypes}
                main={state.main}
                setMain={setMain}
              />
            )}
            {tab === 'classes' && (
              <ClassesTab classes={state.classes} setClasses={setClasses} />
            )}
            {tab === 'filters' && (
              <FiltersTab
                taxonomies={settings.taxonomies}
                filters={state.filters}
                setFilters={setFilters}
              />
            )}
            {tab === 'search' && (
              <SearchTab search={state.search} setSearch={setSearch} />
            )}
            {tab === 'order' && (
              <OrderTab order={state.order} setOrder={setOrder} />
            )}
          </div>
        </div>
      </div>

      <ShortcodeField value={postsShortcode} />

      {hasFilters && <ShortcodeField value={filtersShortcode} />}
    </div>
  );
}
