<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemAdmin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSystemAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'システム管理者を対話的に新規作成します';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('新しいシステム管理者を作成します。');

        $name = $this->ask('名前を入力してください');
        if (empty($name)) {
            $this->error('名前は必須です。処理を中止します。');
            return Command::FAILURE;
        }

        $email = $this->ask('メールアドレスを入力してください');
        if (empty($email)) {
            $this->error('メールアドレスは必須です。処理を中止します。');
            return Command::FAILURE;
        }

        // バリデーション (メール形式と重複チェック)
        $validator = Validator::make(
            ['email' => $email],
            ['email' => 'required|email|unique:system_admins,email']
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            $this->error('処理を中止します。');
            return Command::FAILURE;
        }

        $password = $this->secret('パスワードを入力してください (8文字以上)');
        if (empty($password) || strlen($password) < 8) {
            $this->error('パスワードは8文字以上で入力してください。処理を中止します。');
            return Command::FAILURE;
        }

        // 確認用パスワード
        $passwordConfirm = $this->secret('確認のため、もう一度パスワードを入力してください');
        if ($password !== $passwordConfirm) {
            $this->error('パスワードが一致しません。処理を中止します。');
            return Command::FAILURE;
        }

        // 登録処理
        try {
            $admin = SystemAdmin::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            $this->info("システム管理者 [{$admin->name} / {$admin->email}] の作成が成功しました！");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('データベースエラーが発生しました: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
