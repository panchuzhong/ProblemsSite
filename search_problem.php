<form action="search_problem.php" method="get">
    <label for="problemId">Problem ID:</label>
    <input type="text" id="problemId" name="problemId">
    <input type="submit" value="Search">
</form>

<?php
if (isset($_GET['problemId'])) {
    include 'db.php';
    $problemId = $_GET['problemId'];

    $stmt = $conn->prepare("SELECT Title, Description FROM Problems WHERE ProblemID = ?");
    $stmt->bind_param("i", $problemId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "Title: " . $row["Title"]. " - Description: " . $row["Description"]. "<br>";
        }
    } else {
        echo "No results found for ID " . $problemId;
    }
}
?>

