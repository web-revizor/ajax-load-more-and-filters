import * as fs from 'node:fs';
import * as path from 'node:path';

export function svgSpritePlugin(options = {}) {
  const {
    iconsDir = 'src/icons',
    outputDir = 'template-parts',
    outputName = 'sprite.php',
    typesDir = 'src/components/sharedComponents/Icon',
    typesName = 'sprite-info.ts',
    category = 'common',
  } = options;

  let lastContent = '';
  let lastTypesContent = '';

  const generateTypes = (ids) => {
    const typesPath = path.resolve(typesDir, typesName);

    // Сортуємо так само, як буде виглядати читабельний union/масив
    const sortedIds = [...ids].sort((a, b) => a.localeCompare(b));

    const unionType = sortedIds.map((id) => `    | '${id}'`).join('\n');
    const arrayItems = sortedIds.map((id) => `    '${id}',`).join('\n');

    const typesContent = `export interface SpritesMap {
  ${category}:
${unionType};
}

export const SPRITES_META: { [K in keyof SpritesMap]: SpritesMap[K][] } = {
  ${category}: [
${arrayItems}
  ],
};
`;

    // Запобігаємо зайвому запису, якщо вміст не змінився
    if (typesContent === lastTypesContent) {
      return;
    }

    lastTypesContent = typesContent;

    const typesDirPath = path.dirname(typesPath);
    if (!fs.existsSync(typesDirPath)) {
      fs.mkdirSync(typesDirPath, { recursive: true });
    }

    fs.writeFileSync(typesPath, typesContent);
    console.log(
      `✓ Sprite types generated: ${typesPath} (${sortedIds.length} icons)`
    );
  };

  const generateSprite = () => {
    const iconsPath = path.resolve(iconsDir);
    const outputPath = path.resolve(outputDir, outputName);

    if (!fs.existsSync(iconsPath)) {
      console.warn(`Icons directory not found: ${iconsPath}`);
      return;
    }

    const files = fs
      .readdirSync(iconsPath)
      .filter((file) => file.endsWith('.svg'));

    if (files.length === 0) {
      console.warn('No SVG files found in icons directory');
      return;
    }

    const symbols = [];
    const ids = [];

    for (const file of files) {
      const filePath = path.join(iconsPath, file);
      let content = fs.readFileSync(filePath, 'utf-8');

      const id = path.basename(file, '.svg');
      ids.push(id);

      // Видаляємо тільки XML декларації, DOCTYPE та коментарі
      content = content
        .replace(/<\?xml[^>]*\?>/g, '')
        .replace(/<!DOCTYPE[^>]*>/g, '')
        .replace(/<!--[\s\S]*?-->/g, '')
        .trim();

      // Витягуємо всі атрибути з <svg> тега
      const svgMatch = content.match(/<svg([^>]*)>/);
      let svgAttrs = svgMatch ? svgMatch[1].trim() : '';

      // Видаляємо width та height атрибути
      svgAttrs = svgAttrs
        .replace(/\s*(?:width|height)\s*=\s*(['"])[^'"]*\1/gi, '')
        .trim();

      // Замінюємо <svg> на <symbol> зі збереженням всіх атрибутів
      const svgContent = content
        .replace(
          /<svg[^>]*>/,
          `<symbol id="icon-${id}"${svgAttrs ? ' ' + svgAttrs : ''}>`
        )
        .replace(/<\/svg>/, '</symbol>');

      symbols.push(svgContent);
    }

    // Генеруємо sprite-info.ts на основі тих самих файлів
    generateTypes(ids);

    // Мініфікуємо результат
    const minifiedSymbols = symbols.map((symbol) =>
      symbol
        .replace(/>\s+</g, '><') // Видаляємо пробіли між тегами
        .replace(/\s+/g, ' ') // Множинні пробіли в один
        .trim()
    );

    const spriteContent = `<svg class="hidden" xmlns="http://www.w3.org/2000/svg">${minifiedSymbols.join('')}</svg>`;

    // Перевіряємо чи змінився вміст (запобігаємо циклічному білду)
    if (spriteContent === lastContent) {
      return;
    }

    lastContent = spriteContent;

    // Створюємо директорію якщо не існує
    const outputDirPath = path.dirname(outputPath);
    if (!fs.existsSync(outputDirPath)) {
      fs.mkdirSync(outputDirPath, { recursive: true });
    }

    fs.writeFileSync(outputPath, spriteContent);
    console.log(
      `✓ SVG sprite generated: ${outputPath} (${files.length} icons)`
    );
  };

  return {
    name: 'vite-svg-sprite-plugin',

    configureServer(server) {
      const iconsPath = path.resolve(iconsDir);

      // Генеруємо спрайт при старті dev server
      generateSprite();

      // Спостерігаємо за змінами в іконках
      const watcher = server.watcher;

      watcher.on('change', (file) => {
        if (
          file.includes(iconsDir.replace(/\\/g, '/')) &&
          file.endsWith('.svg')
        ) {
          console.log(`Icon changed: ${path.basename(file)}`);
          generateSprite();
        }
      });

      watcher.on('add', (file) => {
        if (
          file.includes(iconsDir.replace(/\\/g, '/')) &&
          file.endsWith('.svg')
        ) {
          console.log(`Icon added: ${path.basename(file)}`);
          generateSprite();
        }
      });

      watcher.on('unlink', (file) => {
        if (
          file.includes(iconsDir.replace(/\\/g, '/')) &&
          file.endsWith('.svg')
        ) {
          console.log(`Icon removed: ${path.basename(file)}`);
          generateSprite();
        }
      });
    },

    buildEnd() {
      // Генеруємо спрайт при production build
      generateSprite();
    },
  };
}
