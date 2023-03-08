import 'package:flutter/material.dart';

import '../constant.dart';

class FieldInput extends StatelessWidget {
  const FieldInput({Key? key, required this.image, required this.hintText})
      : super(key: key);
  final String image;
  final String hintText;
  @override
  Widget build(BuildContext context) {
    return Stack(
      alignment: Alignment.centerLeft,
      children: [
        Container(
          decoration: BoxDecoration(
              color: kMainColor1.withOpacity(0.5),
              borderRadius: BorderRadius.circular(30)),
          height: 55,
        ),
        Row(
          children: [
            CircleAvatar(
              radius: 30,
              backgroundColor: kMainColor2,
              child: Image.asset('assets/images/$image.png'),
            ),
            const SizedBox(width: 13),
            Expanded(
              child: TextField(
                decoration: InputDecoration(
                    border: InputBorder.none,
                    hintText: hintText,
                    hintStyle: Theme.of(context).textTheme.titleMedium),
              ),
            ),
          ],
        ),
      ],
    );
  }
}
