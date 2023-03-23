import 'package:flutter/material.dart';
import 'package:social/constant.dart';

import './Screens/login_screen.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      theme: ThemeData(
        colorScheme: const ColorScheme.light(secondary: kMainColor2),
        fontFamily: 'Golos',
        textTheme: const TextTheme(
            titleLarge: TextStyle(
              color: Color(0xFF003F5C),
              fontSize: 30,
              fontWeight: FontWeight.w500,
            ),
            titleMedium: TextStyle(color: Colors.grey),
            bodyLarge: TextStyle(
              color: Colors.white,
              fontSize: 24,
              fontWeight: FontWeight.w500,
            ),
            bodyMedium: TextStyle(
              color: Colors.black,
              fontSize: 16,
              fontWeight: FontWeight.w400,
            ),
            bodySmall: TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w400,
            )),
      ),
      home: const LoginScreen(),
    );
  }
}
