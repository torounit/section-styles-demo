# Section Styles Demo

Section styles（`styles/` 配下に置く、`blockTypes` を持つ theme.json パーシャル）が、どの記述をサポートしているかを検証するためのブロックテーマです。

## 起動

```sh
npm install
npm start              # WordPress 安定版 → http://127.0.0.1:9400
npm run start:nightly  # WordPress nightly（trunk）→ http://127.0.0.1:9401
```

どちらもログイン済み（admin / password）で起動します。

## 検証結果（2026-10-28 時点）

WordPress 7.1.3（安定版）と 7.2（trunk）で、フロントとエディタに出力された CSS を確認しました。「スキーマ」列は、Gutenberg trunk の [公式 JSON スキーマ](https://github.com/WordPress/gutenberg/blob/trunk/schemas/json/theme.json)で検証した結果です。

- ✅ フロント・エディタとも出力される
- ⚠️ エディタにだけ出力され、フロントには出ない
- ❌ どちらにも出ない

### section style ファイル（`styles/sections/*/*.json`）に書いた場合

| ファイル | 記述場所 | スキーマ | 7.1.3 | 7.2 | 関連 |
| --- | --- | --- | --- | --- | --- |
| [root-css.json](styles/sections/working/root-css.json) | `styles.css`（`&` ネスト含む） | 有効 | ✅ | ✅ | [PR #62526](https://github.com/WordPress/gutenberg/pull/62526) |
| [root-breakpoints.json](styles/sections/working/root-breakpoints.json) | `styles.@tablet` / `styles.@mobile`（プロパティ・`elements`） | 有効 | ❌ 黙って捨てられる | ✅ | [Trac #65992](https://core.trac.wordpress.org/ticket/65992)（[r63610](https://core.trac.wordpress.org/changeset/63610)）、[#82392](https://github.com/WordPress/gutenberg/issues/82392) / [PR #82403](https://github.com/WordPress/gutenberg/pull/82403)、[theme.json リファレンス](https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/theme-json-reference/theme-json-living.md#styles) |
| [root-breakpoints-css.json](styles/sections/working/root-breakpoints-css.json) | `styles.@mobile.css` | 有効 | ❌ | ✅ | [Trac #65992](https://core.trac.wordpress.org/ticket/65992)（[r63610](https://core.trac.wordpress.org/changeset/63610)）、[#82392](https://github.com/WordPress/gutenberg/issues/82392) / [PR #82403](https://github.com/WordPress/gutenberg/pull/82403) |
| [blocks-breakpoints.json](styles/sections/working/blocks-breakpoints.json) | `styles.blocks.{block}.@tablet` / `@mobile`（`css` 含む） | 有効 | ✅ | ✅ | [Dev note](https://make.wordpress.org/core/2026/08/05/responsive-block-styles-and-configurable-viewports-in-wordpress-7-1/) |
| [blocks-breakpoints-elements.json](styles/sections/working/blocks-breakpoints-elements.json) | `styles.blocks.{block}.@tablet` / `@mobile` 内の `elements.{element}`（`css` 含む） | 有効 | ✅ | ✅ | [Global Settings & Styles](https://github.com/WordPress/gutenberg/blob/trunk/docs/how-to-guides/themes/global-settings-and-styles.md#responsive-styles) |
| [root-breakpoints-blocks.json](styles/sections/not-working/root-breakpoints-blocks.json) | `styles.@mobile.blocks.{block}` | **無効** | ❌ | ❌ | 無効フィクスチャ [stylesBlocksResponsiveStatePropertiesComplete](https://github.com/WordPress/gutenberg/blob/trunk/test/integration/fixtures/schemas/stylesBlocksResponsiveStatePropertiesComplete.json) |
| [elements-breakpoints.json](styles/sections/not-working/elements-breakpoints.json) | `styles.elements.{element}.@tablet` / `@mobile` | **無効** | ⚠️ | ⚠️ | 無効フィクスチャ [stylesElementsPropertiesComplete_breakpoint](https://github.com/WordPress/gutenberg/blob/trunk/test/integration/fixtures/schemas/stylesElementsPropertiesComplete_breakpoint.json) |
| [blocks-elements-breakpoints.json](styles/sections/not-working/blocks-elements-breakpoints.json) | `styles.blocks.{block}.elements.{element}.@tablet` / `@mobile` | **無効** | ⚠️ | ⚠️ | 無効フィクスチャ [stylesElementsPropertiesComplete_blockBreakpoint](https://github.com/WordPress/gutenberg/blob/trunk/test/integration/fixtures/schemas/stylesElementsPropertiesComplete_blockBreakpoint.json) |

7.2 で正常に動作するものは [working/](styles/sections/working/)、そうでないものは [not-working/](styles/sections/not-working/) に置いています。not-working の3ケースは、いずれも公式スキーマで無効とされる書き方です。各ファイルの slug はファイル名と同じで、`is-style-{slug}` として Group ブロックに適用します。title と description には、どのバージョンで動作するかを書いています。

### theme.json に直接書いた場合（参考）

`register_block_style()` で登録したうえで、theme.json の `styles.blocks.core/group.variations.{slug}` に書いた場合です。7.1.3 でだけ確認しました。

| 記述場所 | 7.1.3 |
| --- | --- |
| `….{slug}.@mobile`（プロパティ・`elements`） | ✅ |
| `….{slug}.@mobile.css` | ✅ |
| `….{slug}.@mobile.blocks.{block}` | ❌ |

## ドキュメントとの照合

| 資料 | 内容 | 検証結果との関係 |
| --- | --- | --- |
| [Dev note: Responsive block styles and configurable viewports in WordPress 7.1](https://make.wordpress.org/core/2026/08/05/responsive-block-styles-and-configurable-viewports-in-wordpress-7-1/) | `styles.blocks.{block}.@mobile` と、その中の `:hover` などの疑似セレクタ、ブロックの `style` 属性での `@mobile` を説明。`elements`、variation、パーシャル、`css` には触れていない | `blocks-breakpoints` が 7.1.3 で動くことと一致 |
| [Global Settings & Styles（Block Editor Handbook / Gutenberg trunk）](https://github.com/WordPress/gutenberg/blob/trunk/docs/how-to-guides/themes/global-settings-and-styles.md#responsive-styles) | 「ブロックと要素のレベルで有効なプロパティは `@mobile` / `@tablet` の中に書ける」。例は `blocks.{block}.@mobile` と `blocks.{block}.@mobile.elements.{element}`（`:hover` 含む） | 「要素のレベルで有効なプロパティ」は、ブレークポイントの中に `elements` を書けるという意味で、`elements.{element}.@mobile` の形ではない |
| [theme.json リファレンス（Gutenberg trunk）](https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/theme-json-reference/theme-json-living.md#styles) | パーシャル（`blockTypes` を持つファイル）では、トップレベルの `styles` にも `@mobile` / `@tablet` と疑似セレクタを書ける | 7.2 の結果と一致。7.1.3 には未反映 |
| [公式 JSON スキーマのテスト](https://github.com/WordPress/gutenberg/tree/trunk/test/integration/fixtures/schemas)（`invalidFiles`） | `styles.elements.link.@mobile`、`styles.blocks.core/group.elements.link.@mobile`、通常の theme.json の `styles.@tablet` は無効として扱う | element の2ケースは不具合ではなく仕様外 |
| [Block Style Variations（Theme Handbook）](https://developer.wordpress.org/themes/features/block-style-variations/) | パーシャル、ブレークポイント、`css` のいずれにも触れていない（2023-11 公開） | 情報なし |

### 補足

- パーシャルでのブレークポイント対応は、Gutenberg では [#82392](https://github.com/WordPress/gutenberg/issues/82392) / [PR #82403](https://github.com/WordPress/gutenberg/pull/82403)（Gutenberg 24.1）、core では [#65992](https://core.trac.wordpress.org/ticket/65992)（trunk r63610、2026-09-14）で修正されました。docblock には `@since 7.1.1` とありますが、7.1.1〜7.1.3 にも 7.1 ブランチ（7.1.4-alpha）にも含まれていません。
- 入れ子のブロックをブレークポイントで変えたいときは、`styles.@mobile.blocks.{block}` ではなく `styles.blocks.{block}.@mobile` と書きます。
- element をブレークポイントで変えたいときは、`elements.{element}.@mobile` ではなく `@mobile.elements.{element}` と書きます。前者は公式スキーマでは無効ですが、PHP のサニタイズは通してしまい（7.1.3 / trunk とも `$schema_styles_elements[ $element ][ $breakpoint_state ]` が定義されている）、エディタ（JS）はそれを描画します。一方、フロントの CSS を生成する PHP（`WP_Theme_JSON::get_block_nodes()`）は `blocks.{block}.@mobile.elements.{element}` の形しか処理しません。そのため、エラーも出ずに「エディタでは効くのにフロントでは効かない」状態になります。7.2 で、theme.json のグローバルスタイル（`styles.elements.link.@mobile`、`styles.blocks.core/paragraph.elements.link.@mobile`）でも同じくエディタにだけ出ることを確認しました。
- 7.1.3 のエディタでは、`styles.blocks.{block}.@mobile.css` のセレクタが `:root :where(:root :where(...))` と二重に包まれます。要素にはマッチするので表示には影響しません。7.2 では直っています。
