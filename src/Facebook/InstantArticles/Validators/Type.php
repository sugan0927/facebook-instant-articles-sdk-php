<?hh // strict
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Validators;

use Facebook\InstantArticles\Elements\Element;
use Facebook\InstantArticles\Elements\InstantArticle;

/**
 * Class that have all the typechecks and sizechecks for elements and classes
 * that needs to be well checked.
 *
 * is*() prefixed methods return boolean
 *
 * enforce*() prefixed methods return true for success and throw
 * InvalidArgumentException for the invalid cases.
 */
class Type
{
    /**
     * Auxiliary method that formats the message string and throws the Exception
     */
    private static function throwException(mixed $var, mixed $types_allowed): void
    {
        // stringify the $var parameter
        ob_start();
        var_dump($var);
        $var_str = ob_get_clean();

        // stringify the $types_allowed parameter
        ob_start();
        var_dump($types_allowed);
        $types_str = ob_get_clean();

        throw new \InvalidArgumentException(
            "Method expects this value \n----[\n".$var_str."]----\n".
            " to be one of the types \n====[\n".$types_str."]===="
        );
    }

    /**
     * Method that enforces the vec size to be EXACTLY the $size informed. If
     * the size differs from the $size it will throw InvalidArgumentException
     *
     * @param vec $vec the vec that will be checked
     * @param int $size The EXACTLY size that vec must have
     *
     * @return bool
     */
    public static function enforceArraySize(vec<mixed> $vec, int $size): bool
    {
        return self::isArraySize($vec, $size, true);
    }

    /**
     * Method that checks the vec size to be EXACTLY the $size informed. If
     * the size differs from the $size it will return false, otherwise true.
     * @param vec $vec the vec that will be checked
     * @param int $size The EXACTLY size that vec must have
     * @return true if matches the size, false otherwise
     */
    public static function isArraySize(vec<mixed> $vec, int $size, bool $enforce = false): bool
    {
        $meets_size = count($vec) == $size;
        if ($enforce && !$meets_size) {
            self::throwArrayException($vec, $size, 'Exact size');
        }
        return $meets_size;
    }

    /**
     * Method that enforces the vec to have at least $min_size of elements. If
     * the size is less than $min_size it will throw InvalidArgumentException
     * I.e.: vec (1,2,3), $min_size 3 = true
     * I.e.: vec (1,2,3), $min_size 4 = throws InvalidArgumentException
     *
     * @param vec $vec the vec that will be checked
     * @param int $min_size The EXACTLY size that vec must have
     *
     * @return bool
     *
     * @throws \InvalidArgumentException if $vec doesn't have at least $min_size items
     */
    public static function enforceArraySizeGreaterThan(vec<mixed> $vec, int $min_size): bool
    {
        return self::isArraySizeGreaterThan($vec, $min_size, true);
    }

    /**
     * Method that checks if the vec has at least $min_size of elements. If
     * the size is less than $min_size it will return false.
     * I.e.: vec (1,2,3), $min_size 3 = true
     * I.e.: vec (1,2,3), $min_size 4 = false
     *
     * @param vec $vec the vec that will be checked
     * @param int $min_size The minimum elements the vec must have
     *
     * @return bool true if has at least $min_size, false otherwise
     */
    public static function isArraySizeGreaterThan(vec<mixed> $vec, int $min_size, bool $enforce = false): bool
    {
        $meets_size = count($vec) >= $min_size;
        if ($enforce && !$meets_size) {
            self::throwArrayException($vec, $min_size, 'Minimal size');
        }
        return $meets_size;
    }

    /**
     * Method that enforces the vec to have at most $max_size of elements. If
     * the size is more than $max_size it will throw InvalidArgumentException
     * I.e.: vec (1,2,3), $max_size 3 = true
     * I.e.: vec (1,2,3), $max_size 2 = throws InvalidArgumentException
     *
     * @param vec $vec the vec that will be checked
     * @param int $max_size The maximum number of items the vec can have
     *
     * @return bool
     *
     * @throws \InvalidArgumentException if $vec have more than $max_size items
     */
    public static function enforceArraySizeLowerThan(vec<mixed> $vec, int $max_size): bool
    {
        return self::isArraySizeLowerThan($vec, $max_size, true);
    }

    /**
     * Method that checks if the vec has at most $max_size of elements. If
     * the size is more than $max_size it will return false
     * I.e.: vec (1,2,3), $max_size 3 = true
     * I.e.: vec (1,2,3), $max_size 2 = false
     *
     * @param vec $vec the vec that will be checked
     * @param int $max_size The maximum number of items the vec can have
     * @param boolean $enforce works as Type::enforceArrayMaxSize().
     * @see Type::enforceArrayMaxSize().
     *
     * @return bool true if it has less elements than $max_size, false otherwise
     */
    public static function isArraySizeLowerThan(vec<mixed> $vec, int $max_size, bool $enforce = false): bool
    {
        $meets_size = count($vec) <= $max_size;
        if ($enforce && !$meets_size) {
            self::throwArrayException($vec, $max_size, 'Maximum size');
        }
        return $meets_size;
    }

    /*
     * Utility method that constructs the message about vec sizes an throws.
     */
    private static function throwArrayException(vec<mixed> $vec, int $size, string $message): void
    {
        $error_message =
            'Vec expects a '.$message.' of '.$size.
            ' but received an vec with '.count($vec).' items.';

        throw new \InvalidArgumentException($error_message);
    }

    /**
     * Method that checks if the value is in the possible ones from the set to
     * compare against
     *
     * @param mixed $value The value that will be verified
     * @param vec $universe The universe the $value must be in.
     * @return true if the value is IN the universe, false otherwise.
     */
    public static function isWithin(mixed $value, vec<mixed> $universe, bool $enforce = false): bool
    {
        $within = in_array($value, $universe, true);
        if (!$within && $enforce) {
            self::throwNotWithinException($value, $universe);
        }

        return $within;
    }

    /**
     * Method that enforces the value to be IN the universe informed, if not an
     * exception will be thrown.
     *
     * @param mixed $value The value that will be verified
     * @param vec $universe The universe the $value must be in.
     * @return true if the value is IN the universe, throws Exception otherwise.
     * @throws \InvalidArgumentException if the value not IN the expected universe.
     */
    public static function enforceWithin(mixed $value, vec<mixed> $universe): bool
    {
        return self::isWithin($value, $universe, true);
    }

    private static function throwNotWithinException(mixed $value, vec<mixed> $universe): void
    {
        $value_str = self::stringify($value);
        $universe_str = self::stringify($universe);

        throw new \InvalidArgumentException(
            "Method expects this value \n----[\n".$value_str."]----\n".
            " to be within this universe of values \n====[\n".$universe_str."]===="
        );
    }

    /**
     * Checks the thext if it is empty.
     * Examples:
     * "" => true
     * "    " => true
     * "\n" => true
     * "a" => false
     * "  a  " => false
     * "&nbsp;" => true
     *
     * @param string $text The text that will be checked.
     * @return true if empty, false otherwise.
     */
    public static function isTextEmpty(?string $text): bool
    {
        if ($text === null) {
            return true;
        }
        // Stripes empty spaces, &nbsp;, <br/>, new lines
        $text = preg_replace("/\s+/", "", $text);
        $text = str_replace("&nbsp;", "", $text);

        return strlen($text) === 0;
    }

    /**
     * Auxiliary method that stringify an object as var_dump does.
     * @return string $object var_dump result.
     */
    public static function stringify(mixed $object): string
    {
          // stringify the $object parameter
          return var_export($object, true);
    }

    /**
     * Checks if tag is a DOMNode and also to have the $tagName informed.
     * @param $tag DOMNode the tag element that will be validated
     * @param $tagName string Tag name to be validated
     * @return boolean true if it is Element and from same tagName.
     * @throws InvalidArgumentException in case $tag is not DOMNode.
     */
    public static function isElementTag(\DOMNode $tag, string $tagName): bool
    {
        return $tag->nodeName === $tagName;
    }

    /**
     * Enforces tag to be an DOMNode and also to have the $tagName informed.
     * @param $tag DOMNode the tag element that will be validated
     * @param $tagName string Tag name to be validated
     * @throws InvalidArgumentException in case $tag is not DOMNode or same tagName.
     */
    public static function enforceElementTag(\DOMNode $tag, string $tagName): void
    {
        if (!self::isElementTag($tag, $tagName)) {
            throw new \InvalidArgumentException(
                "Tag <".$tagName."> expected, <".$tag->nodeName."> informed."
            );
        }
    }

    public static function mixedToString(mixed $mix): string
    {
        if ($mix !== null && is_string($mix)) {
            return $mix;
        }
        return "";
    }

    public static function concatVec<T>(vec<T> $first, vec<T> $second): vec<T>
    {
        $result = vec[];
        foreach ($first as $item) {
            $result[] = $item;
        }
        foreach ($second as $item) {
            $result[] = $item;
        }

        return $result;
    }
}
