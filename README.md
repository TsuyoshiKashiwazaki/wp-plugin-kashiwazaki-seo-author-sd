# Kashiwazaki SEO Author Schema Display

[![Version](https://img.shields.io/badge/Version-1.0.4-orange.svg)](https://github.com/tsuyoshikashiwazaki/wp-plugin-kashiwazaki-seo-author-sd/releases/tag/v1.0.4)
[![WordPress](https://img.shields.io/badge/WordPress-5.8+-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPLv2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

著者カード（顔写真・肩書・SNS 等）を記事上下に自動表示し、Article・NewsArticle・BlogPosting・WebPage＋Role・Person の JSON‑LD を生成、E‑E‑A‑Tとリッチリザルトを一括強化するオールインワン SEO プラグイン。

## Description

「Kashiwazaki SEO Author Schema Display」は、著者情報を強化することで検索エンジン評価（E-E-A-T）とリッチリザルト対応を向上させる、オールインワンのWordPressプラグインです。

記事の信頼性と専門性を読者と検索エンジンの両方に明確に伝えるための強力な機能を提供します。

## 主な機能

### 著者ボックスの自動表示
記事の上下、またはその両方に、デザイン済みの著者情報ボックスを自動で挿入します。著者名、顔写真（またはロゴ）、肩書き、プロフィール文、SNSリンクなどを分かりやすく表示できます。

### 詳細な著者データ入力
WordPressのユーザープロフィール画面に専用の入力欄を追加。著者が「人物(Person)」「組織(Organization)」「法人(Corporation)」のいずれであるかを選択でき、それぞれに応じた詳細な情報（職業、所属組織、公式サイトURLなど）を設定できます。

### 高度なJSON-LDスキーマ生成
Googleが推奨する最新の仕様に準拠した構造化データを自動生成します。

- `Article`, `NewsArticle`, `BlogPosting`, `WebPage` から最適なスキーマタイプを選択可能
- 著者の役割（執筆者、監修者など）を明確にする `Role` スキーマに対応
- 他のSEOプラグインとの競合を避けるため、スキーマ出力を無効にするオプションも搭載

### 柔軟な表示設定
著者ボックスを表示する投稿タイプ（投稿、固定ページなど）や、表示位置（記事上/下/両方）を管理画面から簡単に設定できます。

このプラグイン一つで、サイトの信頼性向上、専門性の明示、そして検索結果での視認性アップに繋がる施策を包括的に実行できます。

## Installation

1. 本プラグインのフォルダを `/wp-content/plugins/` ディレクトリにアップロードするか、WordPressの管理画面から「プラグイン」>「新規追加」で "Kashiwazaki SEO Author Schema Display" を検索してインストールします。
2. WordPressの「プラグイン」メニューからプラグインを有効化します。
3. **初期設定を行います。**
   - **全体設定**: 管理画面のサイドバーに追加される `Kashiwazaki SEO ...` メニューから設定ページにアクセスし、著者ボックスの表示位置やスキーマのモードなどを設定します。
   - **著者データ入力**: `ユーザー` > `あなたのプロフィール` を開き、「Kashiwazaki SEO Author Schema Display - 著者データ入力」欄に必要な情報を入力します。

## Frequently Asked Questions

### このプラグインの基本的な設定方法は？

1. **全体設定**: 管理画面メニューの `Kashiwazaki SEO ...` をクリックし、設定ページを開きます。「著者ボックスを表示するページ」や「表示位置」、「構造化データ（スキーマ）」モードなどをサイトの方針に合わせて設定します。
2. **著者データ入力**: `ユーザー` > `あなたのプロフィール` ページで、ご自身の著者データを入力します。他のユーザーのデータを編集する場合は、`ユーザー` > `ユーザー一覧` から対象ユーザーを選択して編集してください。

### 「著者タイプ」とは何ですか？

記事の著者が「人物 (Person)」「組織 (Organization)」「法人 (Corporation)」のどれにあたるかを選択する項目です。ユーザープロフィール編集画面で設定できます。この選択に応じて、生成されるスキーマの型（`@type`）や、プロフィール画面に表示される入力項目（例：「職業」は人物タイプのみ）が自動的に変わります。

### 「構造化データ（スキーマ）」の設定がよく分かりません。どれを選べばいいですか？

サイトの状況や目的に応じて選択してください。

- **スキーマを出力しない**: 他のSEOプラグイン等で既に`Article`スキーマを出力しており、著者ボックスの表示機能だけを使いたい場合に選択します。スキーマの重複を避けることができます。
- **`author`: Role＋Person/Org 参照（推奨）**: **通常はこちらを選択してください。** 著者の「役割（執筆者、監修者など）」を検索エンジンに明確に伝え、E-E-A-Tシグナルを強化する上で最も効果的なモードです。
- **`author`: Person/Org 直埋め込み**: 著者情報をシンプルな構造で埋め込みます。
- **Person/Org 分離参照 (@id 利用)**: `Article`スキーマから著者情報を完全に分離し、`author`以外のプロパティ（例: `reviewedBy`で監修者を示すなど）で関連付けたい上級者向けのモードです。

### 他のSEOプラグインと一緒に使えますか？

はい、利用可能です。ただし、他のSEOプラグインが `Article` や `BlogPosting` などのスキーマを出力している場合、構造化データが重複し、Googleから正しく評価されない可能性があります。

その場合は、どちらかのプラグインでスキーマ出力を無効にしてください。本プラグインでスキーマ出力を止めるには、スキーマ設定を「スキーマを出力しない」に設定します。

## Disclaimer (免責事項)

The author and developer of this plugin, Tsuyoshi Kashiwazaki, is not responsible for any damages or losses that may occur from the use of this plugin. Use this plugin at your own risk. This plugin is provided "as is" without warranty of any kind, expressed or implied.

本プラグインの作者および開発者（柏崎剛）は、本プラグインの使用に起因するいかなる損害や損失についても、一切の責任を負いません。本プラグインの利用は、すべて利用者ご自身の責任において行ってください。本プラグインは、明示または黙示を問わず、いかなる保証も伴わずに「現状有姿」で提供されます。

## 変更履歴

### 1.0.4
* 修正: WordPress公式リポジトリのPlugin Check要件に完全対応（Text Domain統一、WordPress 6.8対応確認）
* 修正: フロントページ表示の重複ロジックを統一（`ksas_display_on_home` に一本化）
* 改善: データベースバージョン管理システムの実装（段階的マイグレーション対応）
* 追加: プラグイン自体のSoftwareApplicationスキーマ出力機能
* 改善: 設定画面UI改善（ホームページ設定を最上位に配置）
* 追加: 多言語対応基盤の整備（/languages ディレクトリ作成）

### 1.0.3
* 新機能: 著者タイプ別データ管理（Person/Organization/Corporation で異なるフィールドセットを使用、旧フィールドとの後方互換性を維持）
* 新機能: プロフィール画像/ロゴ選択用のメディアライブラリ統合（画像プレビュー機能付き）
* 改善: テーマ非依存のスタイル実装（!important ルールによる競合防止）
* 追加: `ksas_get_author_data_by_type()` ヘルパー関数による統一的なデータ取得

### 1.0.2
* 新機能: カテゴリページとタグページでの著者ボックス表示に対応
* 新機能: ショートコード `[ksas_author]` を追加（任意の場所に著者ボックスを表示可能）
* 改善: 著者ボックスの表示位置を調整（他のプラグインとの競合を軽減）
* 改善: カテゴリ・タグページでのスキーマ生成に対応

### 1.0.1
* 修正: 設定画面で「著者ボックスを表示するページ」のチェックをすべて外しても、個別投稿ページで機能が意図せず動作してしまう重大な不具合を修正しました。(`is_singular()`関数に起因する問題への対応)
* 改善: 意図しない動作を防ぐため、各種機能の実行条件判定ロジックを強化し、安定性を向上させました。

### 1.0.0
* Initial public release.
