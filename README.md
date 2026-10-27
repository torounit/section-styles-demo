# Section Styles Demo

Section styles（`styles/sections/*/*.json`）が、どの書き方をサポートしているかの検証用テーマ。

```sh
npm install
npm start              # 安定版 → http://127.0.0.1:9400（admin / password）
npm run start:nightly  # nightly → http://127.0.0.1:9401
```

## 結果（2026-10-28）

✅ 動作する / ⚠️ エディタのみ / ❌ 動作しない

| ファイル | 書き方 | 7.1.3 | 7.2 | スキーマ | 関連 |
| --- | --- | --- | --- | --- | --- |
| [root-css](styles/sections/working/root-css.json) | `styles.css` | ✅ | ✅ | 有効 | [PR #62526](https://github.com/WordPress/gutenberg/pull/62526) |
| [root-breakpoints](styles/sections/working/root-breakpoints.json) | `styles.@mobile`（`css` 含む） | ❌ | ✅ | 有効 | [#65992](https://core.trac.wordpress.org/ticket/65992)、[PR #82403](https://github.com/WordPress/gutenberg/pull/82403) |
| [blocks-breakpoints](styles/sections/working/blocks-breakpoints.json) | `styles.blocks.{block}.@mobile` | ✅ | ✅ | 有効 | [Dev note](https://make.wordpress.org/core/2026/08/05/responsive-block-styles-and-configurable-viewports-in-wordpress-7-1/) |
| [blocks-breakpoints-elements](styles/sections/working/blocks-breakpoints-elements.json) | `styles.blocks.{block}.@mobile.elements.{element}` | ✅ | ✅ | 有効 | [Docs](https://github.com/WordPress/gutenberg/blob/trunk/docs/how-to-guides/themes/global-settings-and-styles.md#responsive-styles) |
| [root-breakpoints-blocks](styles/sections/not-working/root-breakpoints-blocks.json) | `styles.@mobile.blocks.{block}` | ❌ | ❌ | 無効：`@mobile` の中に `blocks` は書けない | [スキーマテスト](https://github.com/WordPress/gutenberg/blob/884f439feac30c3f097e91bf6fb29cfe22a8cb78/test/integration/theme-schema.test.js#L131-L140)で無効扱い（[同じ形の fixture](https://github.com/WordPress/gutenberg/blob/884f439feac30c3f097e91bf6fb29cfe22a8cb78/test/integration/fixtures/schemas/stylesBlocksResponsiveStatePropertiesComplete.json)） |
| [elements-breakpoints](styles/sections/not-working/elements-breakpoints.json) | `styles.elements.{element}.@mobile` | ⚠️ | ⚠️ | 無効：`elements.{element}` の中に `@mobile` は書けない | [スキーマテスト](https://github.com/WordPress/gutenberg/blob/884f439feac30c3f097e91bf6fb29cfe22a8cb78/test/integration/theme-schema.test.js#L131-L140)で無効扱い（[同じ形の fixture](https://github.com/WordPress/gutenberg/blob/884f439feac30c3f097e91bf6fb29cfe22a8cb78/test/integration/fixtures/schemas/stylesElementsPropertiesComplete_breakpoint.json)） |
| [blocks-elements-breakpoints](styles/sections/not-working/blocks-elements-breakpoints.json) | `styles.blocks.{block}.elements.{element}.@mobile` | ⚠️ | ⚠️ | 無効：`elements.{element}` の中に `@mobile` は書けない | [スキーマテスト](https://github.com/WordPress/gutenberg/blob/884f439feac30c3f097e91bf6fb29cfe22a8cb78/test/integration/theme-schema.test.js#L131-L140)で無効扱い（[同じ形の fixture](https://github.com/WordPress/gutenberg/blob/884f439feac30c3f097e91bf6fb29cfe22a8cb78/test/integration/fixtures/schemas/stylesElementsPropertiesComplete_blockBreakpoint.json)） |

`@mobile` は `@tablet` も同じ結果。

## 書き方

| やりたいこと | ✅ こう書く | ❌ こうは書かない |
| --- | --- | --- |
| 入れ子のブロックをブレークポイントで変える | `blocks.{block}.@mobile` | `@mobile.blocks.{block}` |
| element をブレークポイントで変える | `@mobile.elements.{element}` | `elements.{element}.@mobile` |
| 7.1.3 で section 自体をブレークポイントで変える | theme.json の `styles.blocks.core/group.variations.{slug}.@mobile`（`register_block_style()` で登録） | パーシャルの `styles.@mobile` |

## 用語・根拠

| 項目 | 内容 |
| --- | --- |
| スキーマ | Gutenberg trunk の [公式 JSON スキーマ](https://github.com/WordPress/gutenberg/blob/trunk/schemas/json/theme.json)で検証した結果 |
| スキーマテストで無効扱い | theme-schema.test.js は `fixtures/schemas/*.json` をすべて [`invalidFiles` として読み](https://github.com/WordPress/gutenberg/blob/884f439feac30c3f097e91bf6fb29cfe22a8cb78/test/integration/theme-schema.test.js#L12-L15)、[スキーマ検証が `false` になることを確かめている](https://github.com/WordPress/gutenberg/blob/884f439feac30c3f097e91bf6fb29cfe22a8cb78/test/integration/theme-schema.test.js#L131-L140)。同じ形の fixture があり、手元の検証でも無効になった |
| スキーマで無効でもエラーにならない理由 | 実行時の PHP サニタイズは別スキーマで、`elements.{element}.@mobile` を通す |
| ⚠️ になる理由 | エディタ（JS）は描画するが、フロントの PHP（`get_block_nodes()`）は処理しない |
| 7.1.3 で `styles.@mobile` が動かない理由 | パーシャルのサニタイズで捨てられる。trunk の [r63610](https://core.trac.wordpress.org/changeset/63610) で修正、7.1 ブランチには未反映 |

## 既知の問題

| 問題 | 状況 |
| --- | --- |
| 7.1.3 のエディタで `blocks.{block}.@mobile.css` のセレクタが `:root :where(:root :where(…))` になる | 表示に影響なし。7.2 で修正済み |
| ⚠️ は theme.json のグローバルスタイルでも起きる | 7.2 で確認 |
