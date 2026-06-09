<?php
$gameId = uniqid("game_");
$conn = mysqli_connect("localhost", "root", "", "gameDB");
$result = mysqli_query($conn, "SELECT * FROM scores ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Simon Says Game</title>
  <style>
    * { text-align: center; font-family: Arial, Helvetica, sans-serif; }
    .btn {
      height: 150px; width: 150px;
      border: 7px solid black; margin: 2.5rem;
      border-radius: 20%;
    }
    .container { display: flex; justify-content: center; }
    .red { background-color: rgb(181, 78, 78); }
    .yellow { background-color: rgb(192, 192, 71); }
    .green { background-color: rgb(78, 181, 78); }
    .blue { background-color: rgb(78, 78, 181); }
    marquee { background-color: yellow; font-weight: bolder; width: 100%; }
    .flash { background-color: white; }
    .userflash { background-color: green; }
    table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
      margin: 20px auto;
      padding: 8px;
    }
  </style>
</head>
<body>
  <h1>Simon Says Game</h1>
  <h3>Press any key to start the game</h3>
  <p><strong>Game ID:</strong> <?php echo $gameId; ?></p>

  <div class="container">
    <div class="lineone">
      <div class="btn red" id="red">1</div>
      <div class="btn yellow" id="yellow">2</div>
    </div>
    <div class="linetwo">
      <div class="btn green" id="green">3</div>
      <div class="btn blue" id="blue">4</div>
    </div>
  </div>

  <marquee direction="left">
    <i>© Created by Hamza Ali, Hamnah Rasheed & Ahmad Hashmi</i>
  </marquee>

  <h2>Previous Scores</h2>
  <table>
    <tr>
      <th>Game ID</th>
      <th>Score</th>
      <th>Played At</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
      <tr>
        <td><?= htmlspecialchars($row['game_id']) ?></td>
        <td><?= $row['score'] ?></td>
        <td><?= $row['played_at'] ?></td>
      </tr>
    <?php endwhile; ?>
  </table>

  <script>
    const gameId = "<?php echo $gameId; ?>";
    let gameSeq = [], userSeq = [], btns = ['red','green','yellow','blue'];
    let level = 0, started = false;
    let h3 = document.querySelector('h3');

    document.addEventListener("keypress", function () {
      if (!started) {
        started = true;
        levelup();
      }
    });

    function gameflash(btn) {
      btn.classList.add('flash');
      setTimeout(() => btn.classList.remove('flash'), 250);
    }

    function userflash(btn) {
      btn.classList.add('userflash');
      setTimeout(() => btn.classList.remove('userflash'), 250);
    }

    function levelup() {
      userSeq = [];
      level++;
      h3.innerText = `Level ${level}`;
      let rand = Math.floor(Math.random() * 4);
      let randCol = btns[rand];
      let btn = document.querySelector(`.${randCol}`);
      gameSeq.push(randCol);
      gameflash(btn);
    }

    function btnpress() {
      let btn = this;
      userflash(btn);
      let userColor = btn.getAttribute('id');
      userSeq.push(userColor);
      checkAns(userSeq.length - 1);
    }

    document.querySelectorAll('.btn').forEach(btn => {
      btn.addEventListener('click', btnpress);
    });

    function checkAns(idx) {
      if (userSeq[idx] === gameSeq[idx]) {
        if (userSeq.length === gameSeq.length) {
          setTimeout(levelup, 1000);
        }
      } else {
        h3.innerHTML = `Game Over! Your score was <b>${level - 1}</b><br>Press any key to start again.`;
        document.body.style.backgroundColor = 'red';
        setTimeout(() => document.body.style.backgroundColor = 'white', 150);
        saveScore(level - 1);
        reset();
      }
    }

    function reset() {
      started = false;
      gameSeq = [];
      userSeq = [];
      level = 0;
    }

    function saveScore(score) {
      fetch('save_score.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `gameId=${gameId}&score=${score}`
      }).then(() => {
        setTimeout(() => location.reload(), 500);
      });
    }
  </script>
</body>
</html>
