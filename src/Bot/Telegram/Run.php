<?php 

namespace Bot\Telegram;

use Sys\Hub\Singleton;

class Run
{
	/**
	 * @var string
	 */
	private $webhook_input;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->webhook_input = /*'{"update_id":344188011,
"message":{"message_id":8867,"from":{"id":243692601,"first_name":"Ammar","last_name":"F","username":"ammarfaizi2","language_code":"en-US"},"chat":{"id":243692601,"first_name":"Ammar","last_name":"F","username":"ammarfaizi2","type":"private"},"date":1501858530,"reply_to_message":{"message_id":8865,"from":{"id":448907482,"first_name":"Apple Wilder","username":"MyIceTea_Bot"},"chat":{"id":243692601,"first_name":"Ammar","last_name":"F","username":"ammarfaizi2","type":"private"},"date":1501858361,"text":"Hasil pencarian anime :\n20 : Naruto\n442 : Naruto Movie 1: Dai Katsugeki!! Yuki Hime Shinobu Houjou Dattebayo!\n594 : Naruto: Takigakure no Shitou - Ore ga Eiyuu Dattebayo!\n761 : Naruto: Akaki Yotsuba no Clover wo Sagase\n936 : Naruto Movie 2: Dai Gekitotsu! Maboroshi no Chiteiiseki Dattebayo!\n1074 : Naruto Narutimate Hero 3: Tsuini Gekitotsu! Jounin vs. Genin!! Musabetsu Dairansen taikai Kaisai!!\n1735 : Naruto: Shippuuden\n2144 : Naruto Movie 3: Dai Koufun! Mikazuki Jima no Animaru Panikku Dattebayo!\n2248 : Naruto: Dai Katsugeki!! Yuki Hime Shinobu Houjou Dattebayo! Special: Konoha Annual Sports Festival\n2472 : Naruto: Shippuuden Movie 1\n4134 : Naruto Shippuuden: Shippuu! \"Konoha Gakuen\" Den\n4437 : Naruto: Shippuuden Movie 2 - Kizuna\n6325 : Naruto: Shippuuden Movie 3 - Hi no Ishi wo Tsugu Mono\n7367 : Naruto: The Cross Roads\n8246 : Naruto: Shippuuden Movie 4 - The Lost Tower\n10075 : Naruto x UT\n10589 : Naruto: Shippuuden Movie 5 - Blood Prison\n10659 : Naruto Soyokazeden Movie: Naruto to Mashin to Mitsu no Onegai Dattebayo!!\n10686 : Naruto: Honoo no Chuunin Shiken! Naruto vs. Konohamaru!!\n12979 : Naruto SD: Rock Lee no Seishun Full-Power Ninden\n13667 : Naruto: Shippuuden Movie 6 - Road to Ninja\n16870 : The Last: Naruto the Movie\n19511 : Naruto Shippuuden: Sunny Side Battle\n28755 : Boruto: Naruto the Movie\n32365 : Boruto: Naruto the Movie - Naruto ga Hokage ni Natta Hi\n34566 : Boruto: Naruto Next Generations\n\nBerikut ini adalah beberapa anime yang cocok dengan naruto.\n\nKetik /idan [spasi] [id_anime] atau balas dengan id anime untuk menampilkan info anime lebih lengkap.","entities":[{"type":"bold","offset":24,"length":2},{"type":"bold","offset":36,"length":3},{"type":"bold","offset":110,"length":3},{"type":"bold","offset":171,"length":3},{"type":"bold","offset":219,"length":3},{"type":"bold","offset":292,"length":4},{"type":"bold","offset":398,"length":4},{"type":"bold","offset":424,"length":4},{"type":"bold","offset":503,"length":4},{"type":"bold","offset":609,"length":4},{"type":"bold","offset":643,"length":4},{"type":"bold","offset":698,"length":4},{"type":"bold","offset":741,"length":4},{"type":"bold","offset":802,"length":4},{"type":"bold","offset":833,"length":4},{"type":"bold","offset":884,"length":5},{"type":"bold","offset":904,"length":5},{"type":"bold","offset":954,"length":5},{"type":"bold","offset":1036,"length":5},{"type":"bold","offset":1101,"length":5},{"type":"bold","offset":1158,"length":5},{"type":"bold","offset":1209,"length":5},{"type":"bold","offset":1244,"length":5},{"type":"bold","offset":1289,"length":5},{"type":"bold","offset":1322,"length":5},{"type":"bold","offset":1386,"length":5},{"type":"bold","offset":1479,"length":6},{"type":"bot_command","offset":1494,"length":5}]},"text":"34566"}}
';*/file_get_contents("php://input");
	}

	/**
	 * Run.
	 */
	public function run()
	{
        file_put_contents("text.txt", json_encode(json_decode($this->webhook_input), 128));
		print shell_exec("/usr/bin/php ".__DIR__."/../../../run/telegram/run.php \"".str_replace(["\\",'"'],["\\\\",'\"'], $this->webhook_input)."\"");
	}
}