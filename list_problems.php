<?php
include 'db.php';

// Fetch problems along with their solutions and graphs
$sql = "SELECT Problems.ProblemID, Problems.Title, Problems.Description,
       GROUP_CONCAT(Solutions.SolutionText SEPARATOR '|') AS Solutions,
       GROUP_CONCAT(Graphs.FileName SEPARATOR '|') AS FileName,
       GROUP_CONCAT(Graphs.GraphType SEPARATOR '|') AS GraphType
       FROM Problems
       LEFT JOIN Solutions ON Problems.ProblemID = Solutions.ProblemID
       LEFT JOIN Graphs ON Problems.ProblemID = Graphs.ProblemID
       GROUP BY Problems.ProblemID";
$result = $conn->query($sql);

function convertEnumerateToHtml($text) {
    $text = preg_replace('/\\\\begin\{enumerate\}/', '<ol>', $text);
    $text = preg_replace('/\\\\end\{enumerate\}/', '</ol>', $text);
    $text = preg_replace('/\\\\item/', '<li>', $text);
    return $text;
}

function displayGraphs($fileNames, $fileTypes) {
    if (empty($fileNames) || empty($fileTypes)) {
        return '<p>No graph</p>';
    }
    $graphs = explode('|', $fileNames);
    $graphTypes = explode('|', $fileTypes);
    $htmlOutput = '';
    $resourceCounter = 1;

    foreach ($graphs as $index => $graph) {
        $fileType = strtolower($graphTypes[$index]);
        $filePath = "files/" . $fileType . "/" . $graph;
        $htmlOutput .= '<div class="resource-container">';
        $htmlOutput .= '<span class="resource-label">图 ' . $resourceCounter . ':</span>';

        if (in_array($fileType, ['png', 'jpg', 'jpeg', 'bmp'])) {
            $htmlOutput .= '<img src="' . htmlspecialchars($filePath) . '" class="resource-image" alt="Problem Graph ' . $resourceCounter . '" onclick="openModal(this.src);">';
        } else {
            $htmlOutput .= '<a href="' . htmlspecialchars($filePath) . '" class="download-link" download>Download ' . htmlspecialchars(strtoupper($fileType)) . '</a>';
        }
        $htmlOutput .= '</div>';
        $resourceCounter++;
    }
    return $htmlOutput;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>List of Problems</title>
  <script>
    window.MathJax =
    {
      tex: {
      inlineMath: [['$', '$'], ['\\(', '\\)']],
      displayMath: [['$$', '$$']],
      processEscapes: true,
      processEnvironments: true,
      macros: {
        ds: "\\displaystyle",
        bm: ["{\\boldsymbol{#1}}",1]
      }
            },
      svg:
      {
        fontCache: 'global'
      },
      startup:
      {
        ready:
          () => {
            console.log('MathJax is ready');
            MathJax.startup.defaultReady();
            MathJax.startup.promise.then(() => {
                    // Optionally reprocess the whole document if dynamic updates occur
                    // MathJax.typesetPromise();
                  });
          }
      },
      options: {
        skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre'],
        ignoreHtmlClass: 'tex2jax_ignore',
        processHtmlClass: 'tex2jax_process'
      }
    };
  </script>
  <script id="MathJax-script" async src="https://cdn.bootcss.com/mathjax/3.0.5/es5/tex-mml-chtml.js" async></script>
  <script src="script.js"></script>
</head>
<body>

<div class="navbar">
  <a href="index2.html">Home</a>
  <a href="list_problems.php">List Problems</a>
  <a href="search_problem.php">Search Problem</a>
  <a href="add_problem_form.html">Add Problem</a>
</div>

<div id="imageModal" class="modal-overlay" onclick="closeModal()">
    <span class="close-btn" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<div class="container">
  <h2>List of Math and Physics Problems, now we have <?= $result->num_rows ?> problems.</h2>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Description</th>
        <th>Graphs</th>
        <th>Solutions</th>
        <th>Edit</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row["ProblemID"]) ?></td>
            <td><?= htmlspecialchars($row["Title"]) ?></td>
            <td><?= convertEnumerateToHtml(str_replace("\\r\\n", "<br>", $row["Description"])) ?></td>
            <td><?= displayGraphs($row["FileName"], $row["GraphType"]) ?></td>
            <td>
              <?php
              if (!empty($row["Solutions"])) {
                $solutions = explode('|', $row["Solutions"]);
                foreach ($solutions as $solution) {
                  echo htmlspecialchars($solution) . "<br><br>";
                }
              } else {
                echo "No solution yet";
              }
              ?>
            </td>
            <td>
              <button onclick="confirmEdit(<?= $row['ProblemID'] ?>)">Edit</button>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6">No problems found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>
