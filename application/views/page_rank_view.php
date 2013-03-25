<?php
/**
 * @view Page Rank
 */
?>
<div class="row hero-unit">
    <div class="row page-rank-container">
        <div class="span12">
            <form class="form-inline">
                <label class="label">Page Url:</label>
                <input type="text" name="page-url" id="page-url" placeholder="http://example.com/"/>
                <label class="label">Keywords:</label>
                <input type="text" name="page-keywords" id="page-keywords" placeholder="sales"/>
                <button type="button" name="check-page-rank-btn" id="check-page-rank-btn" class="btn">Check Page Rank</button>
            </form>
        </div>
        <div class="row">
            <div class="span8">
                <h5>Page Rank result</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Google Page Rank</th><th>Page Speed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="page-rank">0</td>
                            <td id="page-speed">0/100</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="keywords-result-container" class="row hide">
            <div class="span6">
                <h5>Keywords Search Results</h5>
                <h6>Maximum allowed count of results<code>20</code></h6>
            </div>
            <table id="keywords-result-table" class="keywords-result-table table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Headline</th>
                    <th>Url</th>
                    <th>Page Rank</th>
                    <!--                            <th>Page Speed</th>-->
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>