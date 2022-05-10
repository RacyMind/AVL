<?php

abstract class Entry
{
    public int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}

class Person extends Entry
{
    public string $firstname;
    public string $lastname;


    public function __construct(int $id, string $firstname, string $lastname)
    {
        parent::__construct($id);
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }
}

class Node
{
    public Entry $value;
    public ?Node $left = null;
    public ?Node $right = null;
    public int $height;

    public function __construct(Entry $value)
    {
        $this->value = $value;
        $this->height = 1;
        $this->left = null;
        $this->right = null;
    }

    public function setLeft(Node $left)
    {
        $this->left = $left;
    }

    public function setRight(Node $right)
    {
        $this->right = $right;
    }
}


class AvlTree
{
    public ?Node $root = null;

    public function __construct()
    {
        $this->root = null;
    }

    /**
     * Get the height of given node
     * @param Node|null $node
     * @return int
     */
    //
    private function getHeight(?Node $node): int
    {
        if ($node == null) {
            return 0;
        }
        return $node->height;
    }

    /**
     * Perform the Right rotate operation
     * @param Node $node
     * @return Node|null
     */
    private function rightRotate(Node $node): ?Node
    {
        // Get left child node
        $leftNode = $node->left;
        // Get left node right subtree
        $rightSubtree = $leftNode->right;
        // Update the left and right subtree
        $leftNode->right = $node;
        $node->left = $rightSubtree;
        // Change the height of modified node
        $node->height = max(
                $this->getHeight($node->left),
                $this->getHeight($node->right)) + 1;
        $leftNode->height = max(
                $this->getHeight($leftNode->left),
                $this->getHeight($leftNode->right)) + 1;

        return $leftNode;
    }

    /**
     * Perform the Left Rotate operation
     * @param Node $node
     * @return Node|null
     */
    private function leftRotate(Node $node): ?Node
    {
        // Get right child node
        $rightNode = $node->right;
        // Get right node left subtree
        $leftSubtree = $rightNode->left;
        // Update the left and right subtree
        $rightNode->left = $node;
        $node->right = $leftSubtree;
        // Change the height of modified node
        $node->height = max(
                $this->getHeight($node->left),
                $this->getHeight($node->right)) + 1;
        $rightNode->height = max(
                $this->getHeight($rightNode->left),
                $this->getHeight($rightNode->right)) + 1;

        return $rightNode;
    }

    /**
     * Get the balance factor
     * @param Node|null $node
     * @return int
     */
    private function getBalanceFactor(?Node $node): int
    {
        if ($node == null) {
            return 0;
        }
        return $this->getHeight($node->left) -
            $this->getHeight($node->right);
    }

    /**
     * Recursively, add a node in AVL tree
     * Duplicate keys (data) are not allowed
     * @param Node|null $node
     * @param Entry $value
     * @return Node|null
     */
    public function insert(?Node $node, Entry $value): ?Node
    {
        if ($node == null) {
            // Return a new node
            return new Node($value);
        }
        if ($value->id < $node->value->id) {
            $node->left = $this->insert($node->left, $value);
        } else if ($value->id > $node->value->id) {
            $node->right = $this->insert($node->right, $value);
        } else {
            // When given key data already exists
            return $node;
        }

        // Change the height of current node
        $node->height = 1 + max(
                $this->getHeight($node->left),
                $this->getHeight($node->right));

        // Get balance factor of a node
        $factor = $this->getBalanceFactor($node);

        // LL Case
        if ($factor > 1 && $value->id < $node->left->value->id) {
            return $this->rightRotate($node);
        }
        // LL Case
        if ($factor > 1 && $value->id > $node->left->value->id) {
            $node->left = $this->leftRotate($node->left);
            return $this->rightRotate($node);
        }
        // RR Case
        if ($factor < -1 && $value->id > $node->right->value->id) {
            return $this->leftRotate($node);
        }
        // RR Case
        if ($factor < -1 && $value->id < $node->right->value->id) {
            $node->right = $this->rightRotate($node->right);
            return $this->leftRotate($node);
        }
        return $node;
    }

    public function search(?Node $node, int $id): ?Node
    {
        if ($node === null) {
            return null;
        }
        if($id === $node->value->id) {
            return $node;
        }
        if ($id > $node->value->id) {
            return $this->search($node->right, $id);
        } else {
            return $this->search($node->left, $id);
        }
    }

    // Print the tree in preorder form
    public function preorder($node)
    {
        if ($node != null) {
            printf("%d  ", $node->value->id);
            $this->preorder($node->left);
            $this->preorder($node->right);
        }
    }

    // Print the tree in inorder form
    public function inorder($node)
    {
        if ($node != null) {
            $this->inorder($node->left);
            printf("%d  ", $node->value->id);
            $this->inorder($node->right);
        }
    }

    // Print the tree in postorder form
    public function postorder($node)
    {
        if ($node != null) {
            $this->postorder($node->left);
            $this->postorder($node->right);
            printf("%d  ", $node->value->id);
        }
    }

    public static function main()
    {
        $tree = new AvlTree();
        // Add tree node
        for($i = 0; $i<20; $i++) {
            $tree->root = $tree->insert($tree->root, new Person($i, md5(uniqid(rand(), true)), md5(uniqid(rand(), true))));
        }
        printf("Resultant AVL Tree");
        printf("\nPreorder  : ");
        $tree->preorder($tree->root);
        printf("\nInorder   : ");
        $tree->inorder($tree->root);
        printf("\nPostorder : ");
        $tree->postorder($tree->root);
    }
}

AvlTree::main();