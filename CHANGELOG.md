# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.3]

### Added
- **著者タイプ別データ管理**: 人物、組織、法人それぞれに専用のフィールドで個別管理
- **メディアライブラリ連携**: WordPressメディアライブラリから画像を直接選択可能
- **リアルタイムプレビュー**: 選択した画像を即座にプレビュー表示
- **動的フィールド切り替え**: 著者タイプ選択時に関連フィールドのみを表示
- **データ自動移行**: 既存データを新しいタイプ別フィールドに自動移行

### Fixed
- **テーマCSS干渉解消**: `!important`とCSSリセットによる完全なテーマ独立性
- **Dashiconsフォント問題**: フォントファミリー明示によるアイコン表示不具合の解決
- **GitHubアイコン修正**: 存在しない`dashicons-github`を`dashicons-editor-code`に変更
- **Undefinedエラー修正**: frontend.php内の未定義変数エラーを解消
- **レスポンシブデザイン**: モバイル端末での表示レイアウト改善

### Improved
- **UI/UX向上**: より直感的な管理画面インターフェース
- **CSS最適化**: メディアアップローダー用スタイルの追加
- **エラーハンドリング**: より堅牢なエラー処理とフォールバック機能

### Security
- **入力値検証強化**: 新フィールドに対する適切なサニタイゼーション
- **XSS対策**: 出力時のエスケープ処理改善

## [1.0.2]

### Added
- 基本的な著者カード表示機能
- Schema.org JSON-LD出力
- 管理画面設定パネル

### Fixed
- 初期バージョンの安定性向上
- 基本的なバグ修正

## [1.0.1]

### Added
- 初回リリース
- WordPressプラグインとしての基本機能

---

## Migration Notes

### 1.0.2 → 1.0.3
- 既存の著者データは自動的に新しいタイプ別フィールドに移行されます
- 旧フィールドは互換性のため保持されますが、新フィールドが優先されます
- テーマのCSSカスタマイズがある場合は、新しいCSS構造を確認してください

### Upgrade Process
1. プラグインファイルを更新
2. WordPress管理画面でプラグインを再有効化
3. 自動的にデータベース構造がアップグレードされます
4. ユーザープロフィール画面で新機能を確認

## Compatibility

- **WordPress**: 5.0以降
- **PHP**: 7.4以降  
- **MySQL**: 5.6以降
- **ブラウザ**: Chrome, Firefox, Safari, Edge（最新2バージョン）

## Known Issues

現在のところ、重大な既知の問題はありません。

## Support

- **開発者**: 柏崎剛 (Tsuyoshi Kashiwazaki)
- **ウェブサイト**: https://www.tsuyoshikashiwazaki.jp/
- **バグ報告**: プラグイン設定画面またはウェブサイト経由でご報告ください