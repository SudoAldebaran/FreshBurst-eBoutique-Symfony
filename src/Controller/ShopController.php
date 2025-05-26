<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('shop/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/{id}', name: 'app_category')]
    public function category(int $id, CategoryRepository $categoryRepository, ProductRepository $productRepository): Response
    {
        $category = $categoryRepository->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }
        $products = $productRepository->findBy(['category' => $category]);
        return $this->render('shop/category.html.twig', [
            'category' => $category,
            'products' => $products,
        ]);
    }

    #[Route('/add-to-cart/{id}', name: 'app_add_to_cart')]
    public function addToCart(int $id, ProductRepository $productRepository, CartManager $cartManager): Response
    {
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $cartManager->addToCart($product->getId(), 1);
        $this->addFlash('success', 'Produit ajouté au panier !');

        return $this->redirectToRoute('app_category', ['id' => $product->getCategory()->getId()]);
    }

    #[Route('/cart', name: 'app_cart')]
    public function cart(CartManager $cartManager, ProductRepository $productRepository): Response
    {
        $cart = $cartManager->getCart();
        $cartItems = [];
        $subtotal = 0;
        $shippingCost = 5.00;

        foreach ($cart as $productId => $quantity) {
            $product = $productRepository->find($productId);
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
                $subtotal += $product->getPrice() * $quantity;
            }
        }

        return $this->render('shop/cart.html.twig', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shippingCost' => $shippingCost,
        ]);
    }

    #[Route('/update-cart/{id}', name: 'app_update_cart', methods: ['POST'])]
    public function updateCart(int $id, Request $request, CartManager $cartManager, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $cart = $cartManager->getCart();
        $currentQuantity = $cart[$id] ?? 0;
        $action = $request->request->get('action');

        if ($action === 'increase') {
            $cartManager->updateQuantity($id, $currentQuantity + 1);
            $this->addFlash('success', 'Quantité augmentée !');
        } elseif ($action === 'decrease') {
            $cartManager->updateQuantity($id, $currentQuantity - 1);
            $this->addFlash('success', 'Quantité diminuée !');
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/remove-from-cart/{id}', name: 'app_remove_from_cart')]
    public function removeFromCart(int $id, CartManager $cartManager, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $cartManager->removeFromCart($id);
        $this->addFlash('success', 'Produit supprimé du panier !');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/validate', name: 'app_cart_validate', methods: ['POST'])]
    public function validateCart(Request $request, CartManager $cartManager): Response
    {
        // Vérifie que l'utilisateur est connecté
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour valider une commande.');
        }

        // Vérifie le jeton CSRF pour plus de sécurité
        if ($this->isCsrfTokenValid('validate_cart', $request->request->get('_token'))) {
            // Vide le panier après validation
            $cartManager->clearCart();
            $this->addFlash('success', 'Commande validée avec succès ! Merci pour votre achat.');
            return $this->redirectToRoute('app_cart');
        }

        $this->addFlash('error', 'Erreur lors de la validation de la commande.');
        return $this->redirectToRoute('app_cart');
    }
}