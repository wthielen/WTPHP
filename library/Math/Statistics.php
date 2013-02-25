<?php

namespace WT\Math;

abstract class Statistics
{
    private function __construct() {};
    private function __clone() {};

    // Exception messages
    const NOT_CONSISTENT = "The given array of data points is not an array of Vectors of equal dimension";

    /**
     * The k-means clustering algorithm, also known as Lloyd's algorithm
     * http://en.wikipedia.org/wiki/K-means_clustering#Standard_algorithm
     *
     * Returns an array of detected clusters with the data points assigned to them
     */
    public static function kMeansClusters($data, $iterations = null)
    {
        if (!Vector::consistent($data)) throw new \Exception(self::NOT_CONSISTENT);

        // Get basic data from input
        $data = array_values($data);
        $size = count($data);
        $k = ceil(sqrt($size / 2));

        // Initialize the centroids
        $centroids = array();
        for($i = 0; $i < $k; $i++) $centroids[] = $data[mt_rand(0, $size - 1)];

        $iterate = true;
        while($iterate) {
            $hasChange = false;
            $kAssign = array();
            $firstCentroid = current(array_keys($centroids));

            // Assign data points to centroids to form clusters
            foreach($data as $i => $p) {
                if (!isset($kAssign[$i])) $kAssign[$i] = $firstCentroid;

                $current = $kAssign[$i];
                foreach($centroids as $j => $centroid) {
                    if ($centroid->distance($p) < $current->distance($p)) {
                        $kAssign[$i] = $j;
                        $current = $centroid;
                        $hasChange = true;
                    }
                }

            }

            // Group data points by cluster
            $buckets = array();
            foreach($kAssign as $i => $j) $buckets[$j][$i] = $data[$i];

            // Recalculate clusters' centroids by their points' averages
            foreach($centroids as $i => $p) {
                if (!isset($buckets[$i])) {
                    // Remove cluster if nothing has been assigned to this centroid
                    unset($centroids[$i]);
                    continue;
                }

                $centroids[$i] = Vector::average($buckets[$i]);
            }

            $iterate = $hasChange && (is_null($iterations) ? true : $iterations--);
        }

        // Return data points clustered
        return $buckets;
    }

}
