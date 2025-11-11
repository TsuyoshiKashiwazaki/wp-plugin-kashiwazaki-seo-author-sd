# 🚀 Kashiwazaki SEO Author Schema Display

[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-1.0.6-orange.svg)](https://github.com/TsuyoshiKashiwazaki/wp-plugin-kashiwazaki-seo-author-sd/releases)

WordPressプラグイン「**Kashiwazaki SEO Author Schema Display**」は、著者カード（顔写真・肩書・SNS等）を記事上下に自動表示し、Article・NewsArticle・BlogPosting・WebPage＋Role・Person の JSON‑LD を生成、**E‑E‑A‑T**と**リッチリザルト**を一括強化するオールインワン SEO プラグインです。

## 主な機能

### 🎯 著者情報の自動表示
- **著者カード**: 顔写真、名前、肩書き、所属組織、プロフィール文、SNSリンクを美しく表示
- **表示位置**: 記事上部、記事下部、見出しタグ前、または両方に表示可能
- **レスポンシブ対応**: スマートフォンやタブレットでも適切にレイアウト調整

### 👥 著者タイプ別管理
- **人物 (Person)**: 個人の著者情報（職業、所属組織、プロフィール等）
- **組織 (Organization)**: 組織の情報（組織名、概要、関連リンク等）
- **法人 (Corporation)**: 法人の情報（法人名、事業内容、公式サイト等）

各著者タイプごとに専用のフィールドで管理し、選択されたタイプに応じて適切な情報のみを表示します。

### 📊 Schema.org 対応
- **JSON-LD**: Article、NewsArticle、BlogPosting、WebPageスキーマを自動生成
- **Role構造**: author、editor、supervisor等の役割情報を含む詳細な構造化データ
- **リッチリザルト**: 検索結果での著者情報表示を強化

### 🎨 テーマ独立設計
- **CSS干渉防止**: `!important`を使用した強固なスタイル定義でテーマCSSの影響を回避
- **Dashiconsサポート**: WordPressの標準アイコンフォントを確実に読み込み
- **フォールバック対応**: メディアやアイコンが読み込めない場合の代替表示

### 🖼️ メディアライブラリ連携
- **画像選択**: WordPressメディアライブラリから直接プロフィール画像を選択
- **リアルタイムプレビュー**: 選択した画像を即座にプレビュー表示
- **URL直接入力**: 外部画像URLの直接入力にも対応

### 🔗 SNS・外部リンク対応
- **豊富なアイコン**: 40以上のSNS・サービスに対応したアイコン表示
- **自動判定**: URLから適切なアイコンを自動選択
- **rel属性**: 適切な rel="noopener noreferrer me" を自動付与

## インストール

1. プラグインファイルを `/wp-content/plugins/kashiwazaki-seo-author-sd/` ディレクトリにアップロード
2. WordPress管理画面の「プラグイン」メニューからプラグインを有効化
3. 「設定」→「著者スキーマ表示設定」から各種設定を行う
4. 各ユーザーのプロフィール画面で著者情報を入力

## 使い方

### 基本設定
1. **表示設定**: 著者カードを表示する投稿タイプと位置を選択
2. **スキーマ設定**: JSON-LDの出力モードと記事タイプを設定
3. **デザイン**: 表示位置やスタイルをカスタマイズ

### 著者情報の入力
1. WordPress管理画面の「ユーザー」からプロフィールを編集
2. 「著者タイプ」を選択（人物/組織/法人）
3. 選択したタイプに応じて表示される専用フィールドに情報を入力
4. メディアライブラリから画像を選択またはURL直接入力

### ショートコード使用
```php
[ksas_author user_id="1"]           // 特定のユーザーIDを指定
[ksas_author author="username"]     // ユーザー名を指定
[ksas_author]                      // 現在の投稿の著者を自動選択
```

## 設定項目

### 表示設定
- **対象投稿タイプ**: post, page, カスタム投稿タイプ
- **表示位置**: 記事上部、記事下部、見出し前、両方
- **表示ページ**: フロントページ、カテゴリー、タグ、ホーム

### スキーマ設定
- **出力モード**: author_simple, author_detailed, person_ref, none
- **記事タイプ**: Article, NewsArticle, BlogPosting, WebPage
- **リンクプロパティ**: author, editor, contributor, creator等

### 著者フィールド（タイプ別）

#### 人物タイプ
- 表示名、顔写真、職業・肩書き、所属組織
- 別名、連絡先メール、プロフィールリンク
- プロフィール文、SNS・ウェブサイトURL

#### 組織・法人タイプ
- 組織名・法人名、ロゴ画像、代替名
- 連絡先メール、公式サイトURL
- 概要・事業内容、関連リンク

## 対応SNS・サービス

Facebook, X (Twitter), Instagram, LinkedIn, YouTube, Pinterest, GitHub, GitLab, Medium, note.com, Qiita, Zenn, はてなブログ, LINE, TikTok, Discord, Mastodon, Threads, Behance, Dribbble, SoundCloud, Spotify, Amazon, Wikipedia, Google Scholar 他40以上

## 技術仕様

- **WordPress**: 5.0以降
- **PHP**: 7.4以降
- **依存関係**: Dashicons（WordPress標準）
- **CSS**: テーマ非依存設計
- **JavaScript**: jQuery（WordPress標準）

## 更新履歴

### [1.0.6] - 2025-11-11
- **NEW**: the_content()を使わない投稿タイプの自動検出機能
  - template_redirect フックの動的解析
  - single_template フィルターの動的解析
  - プラグイン提供テンプレートの自動検出
- **IMPROVE**: the_content フィルター条件の堅牢性向上
  - is_main_query() が動作しない環境でも is_singular() でフォールバック
  - REST API, AJAX, Cron, Feed, Embed, XMLRPC リクエストを除外
  - is_admin() チェック追加
- **IMPROVE**: 重複表示防止機能追加（静的変数による処理済み投稿の追跡）
- **IMPROVE**: 開発者用フィルターフック追加（ksas_should_add_author_box_to_content）
- **FIX**: 設定画面に表示されるが実際には動作しない投稿タイプの問題を解決

### Version 1.0.5 (2025-10-08)
- **NEW**: 著者ボックス表示位置を大幅拡張（7パターン → 27パターン）
  - h5, h6の前後、段落ベース、特殊要素（画像/引用/リスト/テーブル）の直後
  - 最後の段落の前後、最後のHTMLタグの直後など
- **NEW**: 基本設定画面をタブ化（表示設定/スキーマ設定/ショートコード）
- **NEW**: 著者タイプ選択時の動的説明文表示機能
- **IMPROVE**: UI改善 - 「著者データ入力 (自身)」→「著者情報を編集」
- **IMPROVE**: UI改善 - 「設定」→「基本設定」に変更
- **IMPROVE**: プロフィール編集画面から基本設定へのリンク追加
- **IMPROVE**: 著者情報編集画面へのアンカーリンク実装
- **FIX**: JavaScript/CSSのキャッシュバスティング実装（filemtime使用）
- **FIX**: 人物/組織/法人タイプの説明をより分かりやすく改善

### Version 1.0.4 (2025-09-09)
- **FIX**: WordPress公式リポジトリのPlugin Check要件に完全対応
- **FIX**: Text Domainをプラグインスラグに統一し、翻訳機能を改善
- **FIX**: 固定フロントページ表示ロジックの重複問題を解決
- **FIX**: ホームページ設定が正しく反映されない不具合を完全修正
- **FIX**: カテゴリ・タグページの意味のない著者表示設定を削除
- **FIX**: ショートコード使用時のCSS適用問題を解決
- **FIX**: WordPress.orgサイトのアイコン表示を改善
- **FIX**: 著者タイプ切り替え時のフィールド表示問題を修正
- **SECURITY**: XSS対策とユーザー入力サニタイゼーションを強化
- **IMPROVE**: 設定UI改善 - ホームページ設定を最上位に移動
- **IMPROVE**: WordPress 6.8対応確認済み
- **IMPROVE**: WordPress Coding Standardsへの完全準拠
- **IMPROVE**: 多言語対応の基盤整備（/languagesディレクトリ作成）

### Version 1.0.3
- **NEW**: 著者タイプ別の個別データ管理機能
- **NEW**: メディアライブラリからの画像選択機能
- **FIX**: テーマCSS干渉問題の完全解決
- **FIX**: GitHubアイコンの表示問題を修正
- **FIX**: Undefinedエラーの修正
- **IMPROVE**: UI/UX改善（リアルタイムプレビュー、動的フィールド切り替え）

### Version 1.0.2
- 基本機能の実装と安定性向上

### Version 1.0.1
- 初回リリース

## ライセンス

GPL-2.0-or-later

このプラグインは GNU General Public License v2 またはそれ以降のバージョンの下で配布されています。

## サポート・開発者

**開発者**: 柏崎剛 (Tsuyoshi Kashiwazaki)  
**ウェブサイト**: https://www.tsuyoshikashiwazaki.jp/  
**サポート**: プラグインに関するご質問や不具合報告は、開発者ウェブサイトまでお問い合わせください。

## 貢献

バグ報告、機能提案、プルリクエストを歓迎します。より良いプラグインにするため、皆様のフィードバックをお待ちしています。

---

**Keywords**: WordPress, SEO, Schema.org, JSON-LD, Author, E-A-T, Rich Results, 著者情報, 構造化データ