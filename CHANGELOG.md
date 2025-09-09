# 変更履歴

## [1.0.4] - 2025-09-09

### Fixed
- WordPress.org Plugin Check要件への完全対応
- Text Domain をプラグインスラグ（kashiwazaki-seo-author-sd）に統一
- フロントページ表示設定の重複ロジックを `ksas_display_on_home` に一本化

### Added
- データベースバージョン管理システム（`ksas_upgrade_database()`）
- プラグイン自体の SoftwareApplication スキーマ出力機能
- 多言語対応基盤（/languages ディレクトリ）

### Improved
- 設定画面UIの改善（ホームページ設定を最上位に配置）
- WordPress 6.8 対応確認済み

## [1.0.3] - 2025-08-07

### Added
- 著者タイプ別データ管理システム（Person/Organization/Corporation ごとに専用フィールド）
- プロフィール画像/ロゴ選択用のWordPressメディアライブラリ統合
- 画像プレビュー機能
- `ksas_get_author_data_by_type()` ヘルパー関数

### Improved
- テーマ非依存のCSS実装（!important ルールによるテーマスタイル競合の完全防止）
- 著者データ取得ロジックの統一化
- 旧フィールド名からの自動移行による完全な後方互換性

## [1.0.2] - 2025-06-25

### Added
- カテゴリページ、タグページ、ホームページでの著者ボックス表示に対応
- ショートコード `[ksas_author]`, `[ksas_author user_id="1"]`, `[ksas_author author="username"]` を追加
- 表示位置に「最初のh1上」「最初のh2上」「最初のh3上」「最初のh4上」オプションを追加

### Improved
- 管理画面にテンプレートファイルでのショートコード使用方法を追加
- カテゴリ・タグページでのスキーマ生成に対応

## [1.0.1] - 2025-06-12

### Fixed
- 設定画面で「著者ボックスを表示するページ」のチェックをすべて外しても、個別投稿ページで機能が意図せず動作してしまう重大な不具合を修正しました。(`is_singular()`関数に起因する問題への対応)

### Improved
- 意図しない動作を防ぐため、各種機能の実行条件判定ロジックを強化し、安定性を向上させました。

## [1.0.0] - 2025-06-08

### Added
- 初回公開リリース
