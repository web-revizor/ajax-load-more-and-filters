import {useState} from 'react';
import {WralmSettings} from "@/src/types/global";
import {TabId} from "@/src/types";
import {useShortcodeBuilder} from "@/src/hooks/useShortcodeBuilder";
import {Tabs} from "@/src/components/Tabs";
import {MainTab} from "@/src/components/MainTab";
import {FiltersTab} from "@/src/components/FiltersTab";
import {ClassesTab} from "@/src/components/ClassesTab";
import {SearchTab} from "@/src/components/SearchTab";
import {OrderTab} from "@/src/components/OrderTab";
import {ShortcodeField} from "@/src/components/ShortcodeField";

interface Props {
    settings: WralmSettings;
}

export function App({settings}: Props) {
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
                <Tabs active={tab}
                      onChange={setTab}/>

                <div className="wralm-panels">
                    {tab === 'main' && (
                        <MainTab postTypes={settings.postTypes}
                                 main={state.main}
                                 setMain={setMain}/>
                    )}
                    {tab === 'classes' && <ClassesTab classes={state.classes}
                                                      setClasses={setClasses}/>}
                    {tab === 'filters' && (
                        <FiltersTab
                            taxonomies={settings.taxonomies}
                            filters={state.filters}
                            setFilters={setFilters}
                        />
                    )}
                    {tab === 'search' && <SearchTab search={state.search}
                                                    setSearch={setSearch}/>}
                    {tab === 'order' && <OrderTab order={state.order}
                                                  setOrder={setOrder}/>}
                </div>
            </div>

            <ShortcodeField value={postsShortcode}/>

            {hasFilters && <ShortcodeField value={filtersShortcode}/>}
        </div>
    );
}
