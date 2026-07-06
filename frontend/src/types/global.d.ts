export interface WralmSettings {
  postTypes: string[];
  taxonomies: string[];
}

declare global {
  interface Window {
    wralmSettings?: WralmSettings;
  }
}

export {};
