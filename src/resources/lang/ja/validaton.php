<?php

return [
  'required' => ':attribute を入力してください。',
  'date_format' => ':attribute は :format の形式で入力してください。',
  'after_or_equal' => ':attribute は :other 以降の時間を指定してください。',
  'max' => [
    'string' => ':attribute は :max 文字以内で入力してください。',
  ],
  'email' => ':attribute の形式が正しくありません。',
  'unique' => 'すでに登録されている :attribute です。',
  'confirmed' => ':attribute が確認用と一致しません。',

  'attributes' => [
    'clock_in_time' => '出勤時間',
    'clock_out_time' => '退勤時間',
    'note' => '備考',
    'break_start_times.0' => '1回目の休憩開始時間',
    'break_end_times.0' => '1回目の休憩終了時間',
    'break_start_times.1' => '2回目の休憩開始時間',
    'break_end_times.1' => '2回目の休憩終了時間',
    'email' => 'メールアドレス',
    'password' => 'パスワード',
  ],
];
