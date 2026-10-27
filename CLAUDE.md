# CLAUDE.md

このファイルは、Claude Code (claude.ai/code) がこのリポジトリで作業する際のガイダンスを提供します。

## 概要

Section styles（`styles/sections/*.json`）の機能検証用テーマ。デザインは目的ではないので、見た目の調整はしない。検証結果は [README.md](README.md) にまとめる。

## 起動

```sh
npm start              # 安定版 → http://127.0.0.1:9400
npm run start:nightly  # nightly → http://127.0.0.1:9401
```

- WordPress のバージョンは blueprint の `preferredVersions.wp` で決まる。CLI の `--wp` は blueprint に上書きされる。
- 2 つのサーバーはホストが同じなので Cookie が衝突する。切り替えたら `/wp-login.php` から admin / password でログインし直す。

## 検証の進め方

1. 1ファイル1ケースで `styles/sections/{working|not-working}/{case}.json` を作る。7.2 で正常に動作するものは `working/`、そうでないものは `not-working/` に置き、`title` と `description` に動作するバージョンを書く。ファイル名・`slug` は検証する記述場所を表す名前にそろえる（例: `styles.blocks.{block}.@mobile` → `blocks-breakpoints`）。値は差が読み取りやすいリテラル（`red` / `blue` など）にする。サーバーの再起動は不要（auto-mount）。
2. エディタで `className: "is-style-{case}"` を付けた Group を作り、ページとして公開する。
3. フロントの `<style>` やエディタの canvas iframe から、`is-style-{case}` を含むルールを抜き出して確認する。
4. 安定版と nightly の両方で確認し、結果を README の表に追記する。
